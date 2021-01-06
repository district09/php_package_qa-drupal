<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\GrumPHP\EventListener;

use Digipolisgent\QA\Drupal\GrumPHP\ConfigFileMerger;
use Digipolisgent\QA\Drupal\GrumPHP\TransactionalFilesystem;
use GrumPHP\Event\TaskEvent;
use GrumPHP\Task\PhpStan;
use GrumPHP\Task\Phpunit;
use GrumPHP\Task\TaskInterface;

/**
 * Listener for GrumPHP task events.
 *
 * This will create a config file on-the-fly based on:
 * - the example config files in qa-drupal.
 * - combined or replaced by the config file in the root of the project.
 *
 * This will prepare a "Drupal installation" so PHPStan & PHPUnit can run from
 * the Drupal directory within the vendor/drupal directory.
 */
final class TaskEventListener
{

    /**
     * Indicates whether the project is an extension.
     *
     * @var bool
     */
    protected $isExtension;

    /**
     * Invoked when a task is run.
     *
     * @param \GrumPHP\Event\TaskEvent $event
     *   The GrumPHP task event.
     */
    public function onRun(TaskEvent $event)
    {
        $task = $event->getTask();

        $this->prepareConfigFile($task);
        $this->prepareExtension($task);
    }

    /**
     * Create the task configuration file.
     *
     * @param \GrumPHP\Task\TaskInterface $task
     *   The GrumPHP task.
     */
    private function prepareConfigFile(TaskInterface $task)
    {
        $configMerger = new ConfigFileMerger();
        $configMerger->mergeTaskConfig($task, $this->isExtension());
        return;
    }

    /**
     * Prepare environment for an extension.
     *
     * Nothing will be setup when the task is not run within an extension.
     *
     * @param \GrumPHP\Task\TaskInterface $task
     *   The GrumPHP task.
     */
    private function prepareExtension(TaskInterface $task): void
    {
        if (!$this->isExtension()) {
            return;
        }

        if ($task instanceof PhpStan) {
            $this->prepareDrupalRoot();
            $this->prepareDrupalSitesDirectory();
        } elseif ($task instanceof Phpunit) {
            $this->prepareDrupalSitesDirectory();
        }
    }

    /**
     * Create some files to mimic a Drupal root (for PHPStan).
     */
    private function prepareDrupalRoot()
    {
        $filesystem = TransactionalFilesystem::getInstance();
        $filesystem->writeFile(
            'vendor/drupal/vendor/autoload.php',
            '<?php return include dirname(__FILE__, 3) . "/autoload.php";'
        );
        $filesystem->writeFile(
            'vendor/drupal/autoload.php',
            '<?php return include dirname(__FILE__, 2) . "/autoload.php";'
        );
        $filesystem->writeFile('vendor/drupal/composer.json', '{}');
    }

    /**
     * Create directories to mimic the Drupal sites directory.
     */
    private function prepareDrupalSitesDirectory()
    {
        $filesystem = TransactionalFilesystem::getInstance();

        if (!$filesystem->exists('vendor/drupal/sites')) {
            $filesystem->mkdir('vendor/drupal/sites');
        }
    }

    /**
     * Checks wether the project is an extension.
     *
     * @return bool
     *   True if the project is an extension.
     */
    private function isExtension(): bool
    {
        if ($this->isExtension === null) {
            $filesystem = TransactionalFilesystem::getInstance();
            $this->isExtension = !$filesystem->exists('web/index.php');
        }

        return $this->isExtension;
    }
}
