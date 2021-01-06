<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\GrumPHP\EventListener;

use Digipolisgent\QA\Drupal\GrumPHP\TransactionalFilesystem;

/**
 * Listener for Symfony console events.
 */
final class ConsoleEventListener
{

    /**
     * Invoked when the console command terminates.
     *
     * This will:
     * - Rollback any filesystem changes.
     */
    public function onTerminate(): void
    {
        TransactionalFilesystem::getInstance()->rollback();
    }

    /**
     * Invoked when the console command throws an unhandled exception.
     *
     * This will:
     * - Rollback any filesystem changes.
     */
    public function onException(): void
    {
        TransactionalFilesystem::getInstance()->rollback();
    }
}
