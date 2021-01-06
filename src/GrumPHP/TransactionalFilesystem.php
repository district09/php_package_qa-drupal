<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\GrumPHP;

use Symfony\Component\Filesystem\Filesystem;

/**
 * A basic transactional filesystem.
 */
final class TransactionalFilesystem {

    /**
     * The filsystem instance.
     *
     * @var \Digipolisgent\QA\Drupal\GrumPHP\TransactionalFilesystem
     */
    private static $instance;

    /**
     * The symfony filesystem.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * A list of created directories.
     *
     * @var string[]
     */
    private $createdDirectories = [];

    /**
     * A list of created or chanced files.
     *
     * @var string[]
     */
    private $writtenFiles = [];

    /**
     * A list of backup files keyed by their original path.
     *
     * @var string[]
     */
    private $backupFiles = [];

    /**
     * Class constructor.
     */
    private function __construct() {
        $this->filesystem = new Filesystem();
    }

    /**
     * Get the filesystem instance.
     *
     * @return static
     */
    public static function getInstance(): TransactionalFilesystem {
        if (!isset(self::$instance)) {
            self::$instance = new static();
        }

        return self::$instance;
    }

    /**
     * Check if a path exists.
     *
     * @param string $path
     *   The path to check.
     *
     * @return bool
     *   True if the path exists.
     */
    public function exists($path): bool {
        return $this->filesystem->exists($path);
    }

    /**
     * Create a directory.
     *
     * @param string $dir
     *   The directory to create.
     */
    public function mkdir($dir): void {
        // Leave if the directory already exists.
        if (is_dir($dir)) {
            return;
        }

        // Find the top most directory that will be created.
        $created_dir = $dir;

        do {
            $tmp = dirname($created_dir);

            if (is_dir($tmp)) {
                break;
            }

            $created_dir = $tmp;
        } while (TRUE);

        // Create it and append it to the list.
        $this->filesystem->mkdir($dir);
        $this->createdDirectories[] = $created_dir;
    }

    /**
     * Write to a file.
     *
     * @param string $file
     *   The file to write to.
     * @param string $content
     *   The file content.
     */
    public function writeFile($file, $content): void {
        // Backup the existing file.
        if (is_file($file)) {
            $this->backupFile($file);
        }

        // Dump the file.
        $this->filesystem->dumpFile($file, $content);

        // Add it to the list of written files.
        $file = (string) $this->filesystem->readlink($file, TRUE);
        $this->writtenFiles[] = $file;
    }

    /**
     * Copy a file.
     *
     * @param string $file_from
     *   The source file.
     * @param string $file_to
     *   The destination file.
     */
    public function copy(string $file_from, string $file_to): void {
        // Backup the existing file.
        if (is_file($file_to)) {
            $this->backupFile($file_to);
        }

        // Copy the file.
        $this->filesystem->copy($file_from, $file_to, TRUE);

        // Add it to the list of written files.
        $file_to = (string) $this->filesystem->readlink($file_to, TRUE);
        $this->writtenFiles[] = $file_to;
    }

    /**
     * Create a file backup.
     *
     * @param string $file
     *   The file to backup.
     */
    protected function backupFile(string $file): void {
        $file = (string) $this->filesystem->readlink($file, TRUE);

        // Leave if the file was created or changed earlier.
        if (in_array($file, $this->writtenFiles, TRUE)) {
            return;
        }

        // Backup the file.
        $backup = $file . '.qa-drupal';
        $this->filesystem->copy($file, $backup, TRUE);
        $this->backupFiles[$file] = $backup;
    }

    /**
     * Commit the changes.
     */
    public function commit(): void {
        foreach ($this->backupFiles as $backup) {
            if ($this->filesystem->exists($backup)) {
                $this->filesystem->remove($backup);
            }
        }

        $this->createdDirectories = [];
        $this->writtenFiles = [];
        $this->backupFiles = [];
    }

    /**
     * Rollback the changes.
     */
    public function rollback(): void {
        // Remove the created directories.
        foreach ($this->createdDirectories as $index => $dir) {
            if ($this->filesystem->exists($dir)) {
                $this->filesystem->remove($dir);
            }

            unset($this->createdDirectories[$index]);
        }

        // Remove the written files.
        foreach ($this->writtenFiles as $index => $file) {
            if ($this->filesystem->exists($file)) {
                $this->filesystem->remove($file);
            }

            unset($this->writtenFiles[$index]);
        }

        // Restore the backup files.
        foreach ($this->backupFiles as $file => $backup) {
            if ($this->filesystem->exists($backup)) {
                $this->filesystem->copy($backup, $file, TRUE);
                $this->filesystem->remove($backup);
            }

            unset($this->backupFiles[$file]);
        }
    }

    /**
     * Commit the changes when the object is destroyed.
     */
    public function __destruct() {
        $this->commit();
    }
}
