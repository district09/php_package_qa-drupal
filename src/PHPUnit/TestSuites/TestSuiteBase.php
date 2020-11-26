<?php

namespace Digipolisgent\QA\Drupal\PHPUnit\TestSuites;

use Drupal\Core\Test\TestDiscovery;
use PHPUnit\Framework\TestSuite;

/**
 * Base class for Drupal test suites.
 */
abstract class TestSuiteBase extends TestSuite
{

  /**
   * Class constructor.
   *
   * @param string $name
   *   The suite name.
   * @param string $suiteNamespace
   *   Suite namespace used to separate test suite.
   *
   * @throws \ReflectionException
   */
    public function __construct($name, $suiteNamespace)
    {
        parent::__construct('', $name);

        // Get the project root.
        $reflection = new \ReflectionClass(get_class($this));
        $root = dirname($reflection->getFileName(), 7);

        file_exists($root . '/web/index.php')
            ? $this->addCustomTestsBySuiteNamespace($root, $suiteNamespace)
            : $this->addTestsBySuiteNamespace($root, $suiteNamespace);
    }

    /**
     * Find and add tests to the suites.
     *
     * @param string $root
     *   Path to the root of the Drupal installation.
     * @param string $suiteNamespace
     *   Subnamespace used to separate test suite.
     */
    protected function addTestsBySuiteNamespace($root, $suiteNamespace)
    {
        $vendor = "$root/vendor";

        foreach (drupal_phpunit_find_extension_directories($root) as $extension_name => $dir) {
            if (strpos($dir, $vendor) === 0) {
                // Exclude the vendor directory.
                continue;
            }

            $tests_path = "$dir/tests/src/$suiteNamespace";

            if (is_dir($tests_path)) {
                $namespace = "Drupal\\Tests\\$extension_name\\$suiteNamespace\\";
                $this->addTestFiles(TestDiscovery::scanDirectory($namespace, $tests_path));
            }
        }
    }

    /**
     * Find and add website tests to the suites.
     *
     * The project is a site, add the tests of all custom extensions.
     *
     * @param string $root
     *   Path to the root of the Drupal installation.
     * @param string $suite_namespace
     *   Subnamespace used to separate test suite.
     */
    private function addCustomTestsBySuiteNamespace(string $root, string $suiteNamespace): void {
        $customRoots = [
            "$root/web/modules/custom",
            "$root/web/themes/custom",
            "$root/web/profiles/custom",
        ];

        foreach ($customRoots as $customRoot) {
            if (is_dir($customRoot)) {
                $this->addTestsBySuiteNamespace($customRoot, $suiteNamespace);
            }
        }
    }
}
