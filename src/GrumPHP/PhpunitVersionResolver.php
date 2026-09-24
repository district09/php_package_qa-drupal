<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\GrumPHP;

use Composer\InstalledVersions;

/**
 * Resolves the PHPUnit major installed in the consumer project.
 *
 * @internal
 */
final class PhpunitVersionResolver {

  /**
   * Gets the installed PHPUnit major version.
   */
  public static function installedMajorVersion(): int {
    $version = InstalledVersions::getVersion('phpunit/phpunit');
    if ($version === NULL) {
      throw new \LogicException('Unable to determine the installed PHPUnit version.');
    }

    if (!preg_match('/^(?:v)?(?<major>\d+)/', $version, $matches)) {
      throw new \LogicException(sprintf(
        'Unable to determine the PHPUnit major version from "%s".',
        $version,
      ));
    }

    $major = (int) $matches['major'];
    if (!in_array($major, [11, 12], TRUE)) {
      throw new \LogicException(sprintf(
        'Unsupported PHPUnit major version %d. Supported versions are 11 and 12.',
        $major,
      ));
    }

    return $major;
  }

}
