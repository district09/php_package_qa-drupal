<?php

namespace Digipolisgent\QA\Drupal\EventSubscriber;

use GrumPHP\Event\RunnerEvent;
use GrumPHP\Event\RunnerEvents;
use GrumPHP\Event\TaskEvent;
use GrumPHP\Event\TaskEvents;
use GrumPHP\Task\Behat;
use GrumPHP\Task\Phpcs;
use GrumPHP\Task\PhpMd;
use GrumPHP\Task\PhpStan;
use GrumPHP\Task\Phpunit;
use GrumPHP\Task\TaskInterface;
use Nette\Neon\Neon;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

/**
 * Event subscriber for GrumPHP events.
 */
class GrumphpEventSubscriber implements EventSubscriberInterface
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
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            RunnerEvents::RUNNER_RUN => 'loadEnvironmentFiles',
            TaskEvents::TASK_RUN => 'createTaskConfig',
            TaskEvents::TASK_SKIPPED => 'removeTaskConfig',
            TaskEvents::TASK_FAILED => 'removeTaskConfig',
            TaskEvents::TASK_COMPLETE => 'removeTaskConfig',
        ];
    }

    /**
     * Load the environment files.
     *
     * @param RunnerEvent $eventThe GrumPHP runner event.
     */
    public function loadEnvironmentFiles(RunnerEvent $event)
    {
        $fs = new Filesystem();
        $env = new Dotenv();

        if ($fs->exists('.env')) {
            $env->load('.env');
        }

        if ($fs->exists('.env.local')) {
            $env->load('.env.local');
        }
    }

    /**
     * Create the task configuration file.
     *
     * @param TaskEvent $event The GrumPHP task event.
     */
    public function createTaskConfig(TaskEvent $event)
    {
        if (!$info = $this->getTaskConfigFileInfo($event->getTask())) {
            return;
        }

        // Candidate configuration files.
        $key_prefix = strtoupper($info['filename']) . '_SKIP_';
        $type_suffix = ($this->isExtension() ? 'extension' : 'site');
        $package_path = __DIR__ . '/../../configs/';

        $candidates = [
            $key_prefix . 'LOCAL' => $info['filename'] . '.local.' . $info['extension'],
            $key_prefix . 'PROJECT' => $info['filename'] . '.' . $info['extension'],
            $key_prefix . 'PACKAGE_TYPE' => $package_path . $info['filename'] . '-' . $type_suffix . '.' . $info['extension'],
            $key_prefix . 'PACKAGE_GLOBAL' => $package_path . $info['filename'] . '.' . $info['extension'],
        ];

        // Search for the candidates and merge or copy them.
        $fs = new Filesystem();
        $data_merged = NULL;

        foreach ($candidates as $env_var => $file) {
            // Ignore if configured to skip or if the file is missing.
            if (!empty($_SERVER[$env_var]) || !$fs->exists($file)) {
                continue;
            }

            // Read and parse the configuration file.
            $data = $this->readTaskConfigFile($info['type'], $file);

            if ($data === FALSE) {
                // Just copy it if not readable.
                $fs->copy($file, $info['grumphp']);
                return;
            }

            // Merge the data.
            if ($data_merged === NULL) {
                $data_merged = $data;
            }
            elseif ($data) {
                $data_merged = array_merge_recursive($data, $data_merged);
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
    public function removeTaskConfig(TaskEvent $event)
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
        }
        elseif ($task instanceof Phpcs) {
            $info = [
                'filename' => 'phpcs',
                'extension' => 'xml',
                'type' => self::FILETYPE_XML,
            ];
        }
        elseif ($task instanceof PhpMd) {
            $info = [
                'filename' => 'phpmd',
                'extension' => 'xml',
                'type' => self::FILETYPE_XML,
            ];
        }
        elseif ($task instanceof PhpStan) {
            $info = [
                'filename' => 'phpstan',
                'extension' => 'neon',
                'type' => self::FILETYPE_NEON,
            ];
        }
        elseif ($task instanceof Phpunit) {
            $info = [
                'filename' => 'phpmd',
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
     * Checks wether the project is an extension.
     *
     * @return bool True if the project is an extension.
     */
    protected function isExtension()
    {
        if ($this->isExtension === NULL) {
            $fs = new Filesystem();
            $this->isExtension = !$fs->exists('web/index.php');
        }

        return $this->isExtension;
    }
}
