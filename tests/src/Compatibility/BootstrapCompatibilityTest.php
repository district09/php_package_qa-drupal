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

}
