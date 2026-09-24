<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\Tests\Compatibility;

use PHPUnit\Framework\TestCase;

/**
 * Verifies the extension bootstrap registers current Drupal test classes.
 */
final class BootstrapCompatibilityTest extends TestCase {

  /**
   * Tests namespace discovery and Drupal's DocumentElement compatibility alias.
   */
  public function testDrupalTestNamespacesAreAvailable(): void {
    self::assertTrue(class_exists('Drupal\\Tests\\DocumentElement'));
    self::assertTrue(class_exists('Drupal\\TestSite\\TestSiteApplication'));
    self::assertTrue(class_exists('Behat\\Mink\\Element\\DocumentElement'));
  }

  /**
   * Tests that extension discovery follows symlinked extension directories.
   */
  public function testExtensionDiscoveryFollowsDirectorySymlinks(): void {
    $directory = sys_get_temp_dir() . '/qa-drupal-bootstrap-' . bin2hex(random_bytes(8));
    $extensionDirectory = $directory . '-extension';
    mkdir($directory, 0777, TRUE);
    mkdir($extensionDirectory, 0777, TRUE);
    file_put_contents($extensionDirectory . '/example.info.yml', "name: Example\ntype: module\n");

    if (!symlink($extensionDirectory, $directory . '/example')) {
      unlink($extensionDirectory . '/example.info.yml');
      rmdir($extensionDirectory);
      rmdir($directory);
      self::markTestSkipped('Directory symlinks are not supported.');
    }

    try {
      $extensions = drupal_phpunit_find_extension_directories($directory);
      self::assertSame(realpath($extensionDirectory), $extensions['example']);
    }
    finally {
      unlink($directory . '/example');
      unlink($extensionDirectory . '/example.info.yml');
      rmdir($extensionDirectory);
      rmdir($directory);
    }
  }

  /**
   * Tests that extension discovery skips directory symlink cycles.
   */
  public function testExtensionDiscoverySkipsDirectorySymlinkCycles(): void {
    $directory = sys_get_temp_dir() . '/qa-drupal-bootstrap-' . bin2hex(random_bytes(8));
    $extensionDirectory = $directory . '/example';
    mkdir($extensionDirectory, 0777, TRUE);
    file_put_contents($extensionDirectory . '/example.info.yml', "name: Example\ntype: module\n");

    if (!symlink($directory, $extensionDirectory . '/loop')) {
      unlink($extensionDirectory . '/example.info.yml');
      rmdir($extensionDirectory);
      rmdir($directory);
      self::markTestSkipped('Directory symlinks are not supported.');
    }

    try {
      $extensions = drupal_phpunit_find_extension_directories($directory);
      self::assertSame(realpath($extensionDirectory), $extensions['example']);
      self::assertCount(1, $extensions);
    }
    finally {
      unlink($extensionDirectory . '/loop');
      unlink($extensionDirectory . '/example.info.yml');
      rmdir($extensionDirectory);
      rmdir($directory);
    }
  }

}
