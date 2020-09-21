<?php

namespace Gent\QA\Drupal\EventSubscriber;

use GrumPHP\Event\RunnerEvent;
use GrumPHP\Event\RunnerEvents;
use GrumPHP\Event\TaskEvent;
use GrumPHP\Event\TaskEvents;
use GrumPHP\Task\Behat;
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
     * Filename of the merged Behat config.
     */
    protected const BEHAT_CONFIG = '.behat-merged.yml';

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            RunnerEvents::RUNNER_RUN => 'loadEnvironmentFiles',
            TaskEvents::TASK_RUN => 'createMergedBehatConfig',
            TaskEvents::TASK_SKIPPED => 'removeMergedBehatConfig',
            TaskEvents::TASK_FAILED => 'removeMergedBehatConfig',
            TaskEvents::TASK_COMPLETE => 'removeMergedBehatConfig',
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
     * Create the merged behat configuration.
     *
     * @param TaskEvent $event The GrumPHP task event.
     */
    public function createMergedBehatConfig(TaskEvent $event)
    {
        if (!$event->getTask() instanceof Behat) {
            return;
        }

        $fs = new Filesystem();

        // Load our configuration.
        $yaml = Yaml::parseFile(__DIR__ . '/../../configs/behat.yml');

        // Merge-in the project configurations.
        if ($fs->exists('behat.yml')) {
            $extra = Yaml::parseFile('behat.yml');

            if ($extra) {
                $yaml = array_merge_recursive($yaml, $extra);
            }
        }

        if ($fs->exists('behat.local.yml')) {
            $extra = Yaml::parseFile('behat.local.yml');

            if ($extra) {
                $yaml = array_merge_recursive($yaml, $extra);
            }
        }

        // Save the merged configuration.
        $fs->dumpFile(self::BEHAT_CONFIG, Yaml::dump($yaml));
    }

    /**
     * Remove the merged behat configuration.
     *
     * @param TaskEvent $event The GrumPHP task event.
     */
    public function removeMergedBehatConfig(TaskEvent $event)
    {
        if (!$event->getTask() instanceof Behat) {
            return;
        }

        $fs = new Filesystem();

        if ($fs->exists(self::BEHAT_CONFIG)) {
            $fs->remove(self::BEHAT_CONFIG);
        }
    }
}
