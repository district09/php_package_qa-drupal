<?php

namespace Digipolisgent\QA\Drupal\GrumPHP\EventListener;

use Digipolisgent\QA\Drupal\GrumPHP\TransactionalFilesystem;
use GrumPHP\Event\TaskEvent;
use GrumPHP\Task\Behat;
use GrumPHP\Task\Phpcs;
use GrumPHP\Task\PhpMd;
use GrumPHP\Task\PhpStan;
use GrumPHP\Task\Phpunit;
use GrumPHP\Task\TaskInterface;
use Nette\Neon\Neon;
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
     * Invoked when a task is run.
     *
     * @param TaskEvent $event The GrumPHP task event.
     */
    public function onRun(TaskEvent $event)
    {
        $task = $event->getTask();

        $this->createTaskConfig($task);

        if (($task instanceof PhpStan || $task instanceof Phpunit) && $this->isExtension()) {
          $this->prepareDrupalRoot();
        }
    }

    /**
     * Create the task configuration file.
     *
     * @param TaskInterface $task The GrumPHP task.
     */
    protected function createTaskConfig(TaskInterface $task)
    {
        if (!$info = $this->getTaskConfigFileInfo($task)) {
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
        $filesystem = TransactionalFilesystem::getInstance();
        $config = null;

        foreach ($candidates as $env_var => $file) {
            // Ignore if configured to skip or if the file is missing.
            if (!empty($_SERVER[$env_var]) || !$filesystem->exists($file)) {
                continue;
            }

            // Load the configuration file.
            $loaded = $this->loadTaskConfigFile($info['type'], $file);

            if ($loaded === false) {
                // Just copy the file if it isn't readable.
                $filesystem->copy($file, $info['grumphp']);

                return;
            }

            // Merge the configuration.
            if ($config === null) {
                $config = $loaded;
            } elseif ($loaded) {
                $config = array_replace_recursive($loaded, $config);
            }
        }

        // Save the merged configuration.
        if ($config !== null) {
            $this->saveTaskConfigFile($info['type'], $info['grumphp'], $config);
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
    protected function loadTaskConfigFile($type, $file)
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
     * @param array|null $data The configuration data.
     */
    protected function saveTaskConfigFile($type, $file, array $config)
    {
        switch ($type) {
            case self::FILETYPE_YAML:
                $config = Yaml::dump($config);
                break;

            case self::FILETYPE_NEON:
                $config = Neon::encode($config, Neon::BLOCK);
                break;
        }

        $filesystem = TransactionalFilesystem::getInstance();
        $filesystem->writeFile($file, $config);
    }

    /**
     * Create some missing files so Drupal can be detected and loaded.
     */
    protected function prepareDrupalRoot()
    {
        $filesystem = TransactionalFilesystem::getInstance();
        $filesystem->writeFile('vendor/drupal/composer.json', '{}');
        $filesystem->writeFile(
            'vendor/drupal/vendor/autoload.php',
            '<?php return include dirname(__FILE__, 3) . "/autoload.php";'
        );
        $filesystem->writeFile(
            'vendor/drupal/autoload.php',
            '<?php return include dirname(__FILE__, 2) . "/autoload.php";'
        );

        if (!$filesystem->exists('vendor/drupal/sites')) {
            $filesystem->mkdir('vendor/drupal/sites');
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
            $filesystem = TransactionalFilesystem::getInstance();
            $this->isExtension = !$filesystem->exists('web/index.php');
        }

        return $this->isExtension;
    }
}
