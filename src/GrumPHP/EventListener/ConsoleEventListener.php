<?php

namespace Digipolisgent\QA\Drupal\GrumPHP\EventListener;

use Digipolisgent\QA\Drupal\GrumPHP\TransactionalFilesystem;

/**
 * Listener for Symfony console events.
 */
class ConsoleEventListener
{

    /**
     * Invoked when the console command terminates.
     */
    public function onTerminate(): void
    {
        // Rollback any filesystem changes.
        TransactionalFilesystem::getInstance()->rollback();
    }

    /**
     * Invoked when the console command throws an unhandled exception.
     */
    public function onException(): void
    {
        // Rollback any filesystem changes.
        TransactionalFilesystem::getInstance()->rollback();
    }
}
