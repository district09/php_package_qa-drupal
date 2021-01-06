<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\GrumPHP;

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
 * Create a config file, as required by the task, on-the-fly.
 *
 * This will create a config file on-the-fly based on:
 * - The example config files in qa-drupal.
 * - Combined or replaced by the config file in the root of the project.
 *
 * NOTE: Config files are stored permanantly as they can be required by IDE
 *       tools.
 */
final class ConfigFileMerger
{

    /**
     * Configuration file types.
     */
    private const FILETYPE_XML = 'xml';
    private const FILETYPE_YAML = 'yaml';
    private const FILETYPE_NEON = 'neon';

    /**
     * Mapping of task types and their configuration files.
     *
     * @var array
     */
    private $taskInfo = [
        Behat::class => [
            'filename' => 'behat',
            'extension' => 'yml',
            'type' => self::FILETYPE_YAML,
        ],
        Phpcs::class => [
            'filename' => 'phpcs',
            'extension' => 'xml',
            'type' => self::FILETYPE_XML,
        ],
        PhpMd::class => [
            'filename' => 'phpmd',
            'extension' => 'xml',
            'type' => self::FILETYPE_XML,
        ],
        PhpStan::class => [
            'filename' => 'phpstan',
            'extension' => 'neon',
            'type' => self::FILETYPE_NEON,
        ],
        Phpunit::class => [
            'filename' => 'phpunit',
            'extension' => 'xml',
            'type' => self::FILETYPE_XML,
        ],
    ];

    /**
     * The symfony filesystem.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        $this->filesystem = new Filesystem();
    }

    /**
     * Create the task configuration file.
     *
     * @param \GrumPHP\Task\TaskInterface $task
     *   The GrumPHP task.
     * @param bool $isExtension
     *   The files need to be merged for an extension.
     */
    public function mergeTaskConfig(
        TaskInterface $task,
        bool $isExtension
    ): void {
        $taskInfo = $this->getTaskConfigFileInfo($task);
        if (!$taskInfo) {
            return;
        }

        // Candidate configuration files.
        $candidates = $this->getConfigFileCandidates($taskInfo, $isExtension);

        // Search for the candidates and merge or copy them.
        $config = null;

        foreach ($candidates as $env_var => $file) {
            // Ignore if configured to skip or if the file is missing.
            if (!empty($_SERVER[$env_var]) || !$this->filesystem->exists($file)) {
                continue;
            }

            // Load the configuration file.
            $loaded = $this->loadTaskConfigFile($taskInfo['type'], $file);

            // Just copy the file if it isn't readable.
            if ($loaded === false) {
                $this->filesystem->copy($file, $taskInfo['grumphp']);
                return;
            }

            // Merge the configuration.
            $config = $config === null
                ? $loaded
                : array_replace_recursive($loaded, $config);
        }

        // Save the merged configuration.
        if ($config !== null) {
            $this->saveTaskConfigFile($taskInfo, $config);
        }
    }

    /**
     * Get some information about the task configuration file.
     *
     * @param \GrumPHP\Task\TaskInterface $task
     *   The GrumPHP task.
     *
     * @return array|null
     *   The task configuration info (filename, extension, type and name of the
     *   temporary merged file for GrumPHP) as associative array.
     */
    private function getTaskConfigFileInfo(TaskInterface $task)
    {
        $taskClass = get_class($task);
        if (empty($this->taskInfo[$taskClass])) {
            return null;
        }

        $info = $this->taskInfo[$taskClass];
        $info['grumphp'] = sprintf(
            '%s.qa-drupal.%s',
            $info['filename'],
            $info['extension']
        );

        return $info;
    }

    /**
     * Get the candidate configuration files to merge from.
     *
     * @param array $taskInfo
     *   The task information to get the candidates for.
     * @param bool $isExtension
     *   Is the task run in an extension project.
     */
    private function getConfigFileCandidates(
        array $taskInfo,
        bool $isExtension
    ): array {
        // Candidate configuration files.
        $key = strtoupper($taskInfo['filename']) . '_SKIP_';
        $type = ($isExtension ? 'extension' : 'site');
        $path = dirname(__FILE__, 3) . '/configs/';

        return [
            $key . 'LOCAL' => $taskInfo['filename'] . '.local.' . $taskInfo['extension'],
            $key . 'PROJECT' => $taskInfo['filename'] . '.' . $taskInfo['extension'],
            $key . 'PACKAGE_TYPE' => $path . $taskInfo['filename'] . '-' . $type . '.' . $taskInfo['extension'],
            $key . 'PACKAGE_GLOBAL' => $path . $taskInfo['filename'] . '.' . $taskInfo['extension'],
        ];
    }

    /**
     * Read and parse a task configuration file.
     *
     * @param string $type
     *   The file type.
     * @param string $file
     *   Path to the file.
     *
     * @return array|false
     *   The configuration data or false if not supported.
     */
    private function loadTaskConfigFile($type, $file)
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
     * @param array $taskInfo
     *   The task info.
     * @param array|null $data
     *   The configuration data.
     */
    private function saveTaskConfigFile($taskInfo, ?array $config)
    {
        $type = $taskInfo['type'];
        $file = $taskInfo['grumphp'];

        switch ($type) {
            case self::FILETYPE_YAML:
                $config = Yaml::dump($config);
                break;

            case self::FILETYPE_NEON:
                $config = Neon::encode($config, Neon::BLOCK);
                break;
        }

        $this->filesystem->dumpFile($file, $config);
    }
}
