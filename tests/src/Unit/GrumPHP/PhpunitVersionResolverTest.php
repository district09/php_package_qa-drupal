<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\Tests\Unit\GrumPHP;

use Digipolisgent\QA\Drupal\GrumPHP\PhpunitVersionResolver;
use PHPUnit\Framework\TestCase;
use PHPUnit\Runner\Version;

/**
 * Tests consumer-project PHPUnit version resolution.
 */
final class PhpunitVersionResolverTest extends TestCase {

  /**
   * Tests that Composer metadata identifies the active project runner.
   */
  public function testInstalledMajorVersionMatchesProjectRunner(): void {
    self::assertSame(
      Version::majorVersionNumber(),
      PhpunitVersionResolver::installedMajorVersion(),
    );
  }

}
