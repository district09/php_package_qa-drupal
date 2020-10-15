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
     * @param string $name The suite name.
     * @param string $suite_namespace Subnamespace used to separate test suite.
     */
    public function __construct($name, $suite_namespace)
    {
        parent::__construct('', $name);

        // Get the project root.
        $root = dirname(__DIR__, 6);

        if (!file_exists($root . '/web/index.php')) {
            // The project is an extension, add all tests.
            $this->addTestsBySuiteNamespace($root, $suite_namespace);
        } else {
            // The project is a site, add the tests of all custom extensions.
            $roots = [
                $root . '/web/modules/custom',
                $root . '/web/themes/custom',
                $root . '/web/profiles/custom',
            ];

            foreach ($roots as $root) {
                if (is_dir($root)) {
                    $this->addTestsBySuiteNamespace($root, $suite_namespace);
                }
            }
        }
    }

    /**
     * Find and add tests to the suites.
     *
     * @param string $root Path to the root of the Drupal installation.
     * @param string $suite_namespace Subnamespace used to separate test suite.
     */
    protected function addTestsBySuiteNamespace($root, $suite_namespace)
    {
        foreach (drupal_phpunit_find_extension_directories($root) as $extension_name => $dir) {
            $tests_path = "$dir/tests/src/$suite_namespace";

            if (is_dir($tests_path)) {
                $namespace = "Drupal\\Tests\\$extension_name\\$suite_namespace\\";
                $this->addTestFiles(TestDiscovery::scanDirectory($namespace, $tests_path));
            }
        }
    }
}
