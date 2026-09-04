<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\Tests\Unit\GrumPHP;

use Digipolisgent\QA\Drupal\GrumPHP\TransactionalFilesystem;
use PHPUnit\Framework\TestCase;

/**
 * Tests transactional filesystem commit and rollback behavior.
 */
final class TransactionalFilesystemTest extends TestCase {
  /**
   * Filesystem under test.
   */
  private TransactionalFilesystem $filesystem;

  /**
   * Temporary project directory used by a test.
   */
  private string $workingDirectory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->filesystem = TransactionalFilesystem::getInstance();
    $this->filesystem->commit();
    $this->workingDirectory = sys_get_temp_dir() . '/qa-drupal-' . bin2hex(random_bytes(8));
    mkdir($this->workingDirectory, 0777, TRUE);
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    $this->filesystem->rollback();
    $this->removeDirectory($this->workingDirectory);

    parent::tearDown();
  }

  /**
   * Tests that rollback restores original state.
   */
  public function testRollbackRestoresAndRemovesFilesystemChanges(): void {
    $existingFile = $this->workingDirectory . '/existing.txt';
    $newFile = $this->workingDirectory . '/new.txt';
    $newDirectory = $this->workingDirectory . '/nested/directory';
    file_put_contents($existingFile, 'before');

    $this->filesystem->writeFile($existingFile, 'after');
    $this->filesystem->writeFile($newFile, 'new');
    $this->filesystem->mkdir($newDirectory);
    $this->filesystem->rollback();

    self::assertSame('before', file_get_contents($existingFile));
    self::assertFileDoesNotExist($newFile);
    self::assertDirectoryDoesNotExist($newDirectory);
    self::assertFileDoesNotExist($existingFile . '.qa-drupal');
  }

  /**
   * Tests that commit retains changes and removes backups.
   */
  public function testCommitKeepsChangesAndRemovesBackupFiles(): void {
    $existingFile = $this->workingDirectory . '/existing.txt';
    file_put_contents($existingFile, 'before');

    $this->filesystem->writeFile($existingFile, 'after');
    $this->filesystem->commit();

    self::assertSame('after', file_get_contents($existingFile));
    self::assertFileDoesNotExist($existingFile . '.qa-drupal');
  }

  /**
   * Recursively removes a temporary test directory.
   */
  private function removeDirectory(string $directory): void {
    if (!is_dir($directory)) {
      return;
    }

    $entries = scandir($directory);
    if ($entries === FALSE) {
      return;
    }

    foreach (array_diff($entries, ['.', '..']) as $entry) {
      $path = $directory . '/' . $entry;
      is_dir($path) ? $this->removeDirectory($path) : unlink($path);
    }

    rmdir($directory);
  }

}
