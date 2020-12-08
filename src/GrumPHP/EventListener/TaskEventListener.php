<?php

namespace Digipolisgent\QA\Drupal\GrumPHP\EventListener;

use GrumPHP\Event\TaskEvent;
use GrumPHP\Task\Behat;
use GrumPHP\Task\Phpcs;
use GrumPHP\Task\PhpMd;
use GrumPHP\Task\PhpStan;
use GrumPHP\Task\Phpunit;
use GrumPHP\Task\TaskInterface;
use Nette\Neon\Neon;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Listener for GrumPHP task events.
 */
class TaskEventListener
{
    /**
     * Configuration file types.
     */
    protected const FILETYPE_XML = 'xml';
    protected const FILETYPE_YAML = 'yaml';
    protected const FILETYPE_NEON = 'neon';

    /**
     * Indicates whether the project is an extension.
     *
     * @var bool
     */
    protected $isExtension;

    /**
     * Invoked when a GrumPHP task is started.
     *
     * @param TaskEvent $event The GrumPHP task event.
     */
    public function onTaskStart(TaskEvent $event)
    {
        $this->createTaskConfig($event);

        if ($this->isExtension()) {
            $task = $event->getTask();

            if ($task instanceof PhpStan) {
                $this->preparePhpStanDrupalRoot();
            } elseif ($task instanceof Phpunit) {
                $this->createSitesDirectory('phpunit');
            }
        }
    }

    /**
     * Invoked when a GrumPHP task ends.
     *
     * @param TaskEvent $event The GrumPHP task event.
     */
    public function onTaskEnd(TaskEvent $event)
    {
        $this->removeTaskConfig($event);

        if ($this->isExtension()) {
            $task = $event->getTask();

            if ($task instanceof PhpStan) {
                $this->cleanupPhpStanDrupalRoot();
            } elseif ($task instanceof Phpunit) {
                $this->removeSitesDirectory('phpunit');
            }
        }
    }

    /**
     * Create the task configuration file.
     *
     * @param TaskEvent $event The GrumPHP task event.
     */
    protected function createTaskConfig(TaskEvent $event)
    {
        if (!$info = $this->getTaskConfigFileInfo($event->getTask())) {
            return;
        }

        // Candidate configuration files.
        $key_prefix = strtoupper($info['filename']) . '_SKIP_';
        $type_suffix = ($this->isExtension() ? 'extension' : 'site');
        $package_path = __DIR__ . '/../../../configs/';

        $candidates = [
            $key_prefix . 'LOCAL' => $info['filename'] . '.local.' . $info['extension'],
            $key_prefix . 'PROJECT' => $info['filename'] . '.' . $info['extension'],
            $key_prefix . 'PACKAGE_TYPE' => $package_path . $info['filename'] . '-' . $type_suffix . '.' . $info['extension'],
            $key_prefix . 'PACKAGE_GLOBAL' => $package_path . $info['filename'] . '.' . $info['extension'],
        ];

        // Search for the candidates and merge or copy them.
        $fs = new Filesystem();
        $data_merged = null;

        foreach ($candidates as $env_var => $file) {
            // Ignore if configured to skip or if the file is missing.
            if (!empty($_SERVER[$env_var]) || !$fs->exists($file)) {
                continue;
            }

            // Read and parse the configuration file.
            $data = $this->readTaskConfigFile($info['type'], $file);

            if ($data === false) {
                // Just copy it if not readable.
                $fs->copy($file, $info['grumphp']);
                return;
            }

            // Merge the data.
            if ($data_merged === null) {
                $data_merged = $data;
            } elseif ($data) {
                $data_merged = array_replace_recursive($data, $data_merged);
            }
        }

        // Save the configuration file.
        $this->writeTaskConfigFile($info['type'], $info['grumphp'], $data_merged);
    }

    /**
     * Remove the task configuration file.
     *
     * @param TaskEvent $event The GrumPHP task event.
     */
    protected function removeTaskConfig(TaskEvent $event)
    {
        if (!$info = $this->getTaskConfigFileInfo($event->getTask())) {
            return;
        }

        $fs = new Filesystem();

        if ($fs->exists($info['grumphp'])) {
            $fs->remove($info['grumphp']);
        }
    }

    /**
     * Get some information about the task configuration file.
     *
     * @param TaskInterface $task The GrumPHP task.
     *
     * @return array|null The task configuration info (filename, extension, type and name of the
     *                    temporary merged file for GrumPHP) as associative array.
     */
    protected function getTaskConfigFileInfo(TaskInterface $task)
    {
        $info = null;

        if ($task instanceof Behat) {
            $info = [
                'filename' => 'behat',
                'extension' => 'yml',
                'type' => self::FILETYPE_YAML,
            ];
        } elseif ($task instanceof Phpcs) {
            $info = [
                'filename' => 'phpcs',
                'extension' => 'xml',
                'type' => self::FILETYPE_XML,
            ];
        } elseif ($task instanceof PhpMd) {
            $info = [
                'filename' => 'phpmd',
                'extension' => 'xml',
                'type' => self::FILETYPE_XML,
            ];
        } elseif ($task instanceof PhpStan) {
            $info = [
                'filename' => 'phpstan',
                'extension' => 'neon',
                'type' => self::FILETYPE_NEON,
            ];
        } elseif ($task instanceof Phpunit) {
            $info = [
                'filename' => 'phpunit',
                'extension' => 'xml',
                'type' => self::FILETYPE_XML,
            ];
        }

        if ($info) {
            $info['grumphp'] = '.' . $info['filename'] . '.qa-drupal.' . $info['extension'];
        }

        return $info;
    }

    /**
     * Read and parse a task configuration file.
     *
     * @param string $type The file type.
     * @param string $file Path to the file.
     *
     * @return array|false The configuration data or false if not supported.
     */
    protected function readTaskConfigFile($type, $file)
    {
        switch ($type) {
            case self::FILETYPE_YAML:
                return Yaml::parseFile($file);

            case self::FILETYPE_NEON:
                return Neon::decode(file_get_contents($file));
        }

        return false;
    }

    /**
     * Write a config file.
     *
     * @param string $type The file type.
     * @param string $file Path to the file.
     *
     * @param array|null $data The configuration data.
     */
    protected function writeTaskConfigFile($type, $file, array $data)
    {
        switch ($type) {
            case self::FILETYPE_YAML:
                $data = Yaml::dump($data);
                break;

            case self::FILETYPE_NEON:
                $data = Neon::encode($data, Neon::BLOCK);
                break;

            default:
                $data = '';
                break;
        }

        $fs = new Filesystem();
        $fs->dumpFile($file, $data);
    }

    /**
     * Create some files to mimic a Drupal root for PHPStan.
     */
    protected function preparePhpStanDrupalRoot()
    {
        $fs = new Filesystem();
        $fs->dumpFile(
            'vendor/drupal/vendor/autoload.php',
            '<?php return include dirname(__FILE__, 3) . "/autoload.php";'
        );
        $fs->dumpFile('vendor/drupal/autoload.php', '<?php return include dirname(__FILE__, 2) . "/autoload.php";');
        $fs->dumpFile('vendor/drupal/composer.json', '{}');

        $this->createSitesDirectory('phpstan');
    }

    /**
     * Remove the files that mimic a Drupal root for PHPStan.
     */
    protected function cleanupPhpStanDrupalRoot()
    {
        $fs = new Filesystem();
        $paths = [
            'vendor/drupal/vendor',
            'vendor/drupal/autoload.php',
            'vendor/drupal/composer.json',
        ];

        foreach ($paths as $path) {
            if ($fs->exists($path)) {
                $fs->remove($path);
            }
        }

        $this->removeSitesDirectory('phpstan');
    }

    /**
     * Create the sites directory.
     *
     * @param string $lock Name of the lock file.
     */
    protected function createSitesDirectory($lock)
    {
        $fs = new Filesystem();

        if (!$fs->exists('vendor/drupal/sites')) {
            $fs->mkdir('vendor/drupal/sites');
        }

        $fs->dumpFile('vendor/drupal/sites/.' . $lock . '.qa-drupal.lock', '');
    }

    /**
     * Remove the sites directory.
     *
     * @param string $lock Name of the lock file.
     */
    protected function removeSitesDirectory($lock)
    {
        $fs = new Filesystem();
        $lock = 'vendor/drupal/sites/.' . $lock . '.qa-drupal.lock';

        if ($fs->exists($lock)) {
            $fs->remove($lock);

            if (!glob('vendor/drupal/sites/.*.qa-drupal.lock')) {
                $fs->remove('vendor/drupal/sites');
            }
        }
    }

    /**
     * Checks wether the project is an extension.
     *
     * @return bool True if the project is an extension.
     */
    protected function isExtension()
    {
        if ($this->isExtension === null) {
            $fs = new Filesystem();
            $this->isExtension = !$fs->exists('web/index.php');
        }

        return $this->isExtension;
    }
}
