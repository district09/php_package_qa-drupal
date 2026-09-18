<?php

declare(strict_types=1);

namespace Digipolisgent\QA\Drupal\Tests\Unit\GrumPHP;

use Digipolisgent\QA\Drupal\GrumPHP\ConfigFileMerger;
use Digipolisgent\QA\Drupal\GrumPHP\PhpunitVersionResolver;
use GrumPHP\Task\PhpStan;
use GrumPHP\Task\Phpunit;
use Nette\Neon\Neon;
use PHPUnit\Framework\TestCase;

/**
 * Tests merging project-specific GrumPHP task configuration.
 */
final class ConfigFileMergerTest extends TestCase {
  private const SKIP_VARIABLES = [
    'PHPSTAN_SKIP_LOCAL',
    'PHPSTAN_SKIP_PROJECT',
    'PHPSTAN_SKIP_PACKAGE_TYPE',
    'PHPSTAN_SKIP_PACKAGE_GLOBAL',
  ];

  /**
   * Original working directory restored after each test.
   */
  private string $originalWorkingDirectory;

  /**
   * Temporary project directory used by a test.
   */
  private string $workingDirectory;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->originalWorkingDirectory = (string) getcwd();
    $this->workingDirectory = sys_get_temp_dir() . '/qa-drupal-' . bin2hex(random_bytes(8));
    mkdir($this->workingDirectory, 0777, TRUE);
    chdir($this->workingDirectory);

    foreach (self::SKIP_VARIABLES as $variable) {
      unset($_SERVER[$variable], $_ENV[$variable]);
    }

    // Package defaults are tested separately by CI dependency jobs. These
    // tests isolate project/local precedence and their skip controls.
    $_SERVER['PHPSTAN_SKIP_PACKAGE_TYPE'] = '1';
    $_SERVER['PHPSTAN_SKIP_PACKAGE_GLOBAL'] = '1';
  }

  /**
   * {@inheritdoc}
   */
  protected function tearDown(): void {
    foreach (self::SKIP_VARIABLES as $variable) {
      unset($_SERVER[$variable], $_ENV[$variable]);
    }

    chdir($this->originalWorkingDirectory);
    $this->removeDirectory($this->workingDirectory);

    parent::tearDown();
  }

  /**
   * Tests that local configuration has the highest precedence.
   */
  public function testLocalConfigurationOverridesProjectConfiguration(): void {
    file_put_contents('phpstan.neon', "parameters:\n  level: 5\n  tmpDir: project\n");
    file_put_contents('phpstan.local.neon', "parameters:\n  level: 8\n");

    $this->mergePhpStanConfiguration();

    $configuration = Neon::decode((string) file_get_contents('phpstan.qa-drupal.neon'));
    self::assertSame(8, $configuration['parameters']['level']);
    self::assertSame('project', $configuration['parameters']['tmpDir']);
  }

  /**
   * Tests that a skip variable omits the corresponding configuration.
   */
  public function testSkipVariableExcludesLocalConfiguration(): void {
    file_put_contents('phpstan.neon', "parameters:\n  level: 5\n");
    file_put_contents('phpstan.local.neon', "parameters:\n  level: 8\n");
    $_SERVER['PHPSTAN_SKIP_LOCAL'] = '1';

    $this->mergePhpStanConfiguration();

    $configuration = Neon::decode((string) file_get_contents('phpstan.qa-drupal.neon'));
    self::assertSame(5, $configuration['parameters']['level']);
  }

  /**
   * Tests that the generated PHPUnit configuration matches the runner major.
   */
  public function testPhpunitConfigurationUsesCurrentRunnerSchema(): void {
    $task = (new \ReflectionClass(Phpunit::class))->newInstanceWithoutConstructor();
    (new ConfigFileMerger())->mergeTaskConfig($task, TRUE);

    $configuration = (string) file_get_contents('phpunit.qa-drupal.xml');
    $schemaVersion = PhpunitVersionResolver::installedMajorVersion() === 11 ? '11.5' : '12.5';
    self::assertStringContainsString(
      "https://schema.phpunit.de/$schemaVersion/phpunit.xsd",
      $configuration
    );
  }

  /**
   * Tests that extension test discovery is limited to project code.
   */
  public function testExtensionPhpunitConfigurationExcludesVendorPaths(): void {
    $task = (new \ReflectionClass(Phpunit::class))->newInstanceWithoutConstructor();
    (new ConfigFileMerger())->mergeTaskConfig($task, TRUE);

    $configuration = (string) file_get_contents('phpunit.qa-drupal.xml');
    $document = new \DOMDocument();
    self::assertTrue($document->loadXML($configuration));
    self::assertStringContainsString('<directory suffix="Test.php">tests/src</directory>', $configuration);
    self::assertStringNotContainsString('tests/src/Kernel', $configuration);
    self::assertStringNotContainsString('<directory suffix=".php">./**/src', $configuration);
    self::assertStringNotContainsString('>./</directory>', $configuration);
  }

  /**
   * Tests that site source exclusions remain inside custom extension paths.
   */
  public function testSitePhpunitConfigurationExcludesVendorPaths(): void {
    $task = (new \ReflectionClass(Phpunit::class))->newInstanceWithoutConstructor();
    (new ConfigFileMerger())->mergeTaskConfig($task, FALSE);

    $configuration = (string) file_get_contents('phpunit.qa-drupal.xml');
    self::assertStringContainsString('web/modules/custom/**/tests', $configuration);
    self::assertStringNotContainsString('>./</directory>', $configuration);
  }

  /**
   * Runs the configuration merger with a PHPStan task instance.
   */
  private function mergePhpStanConfiguration(): void {
    $task = (new \ReflectionClass(PhpStan::class))->newInstanceWithoutConstructor();
    (new ConfigFileMerger())->mergeTaskConfig($task, TRUE);
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
