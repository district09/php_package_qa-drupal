<?php

/**
 * @file
 * Bootstrap for PHPUnit tests run within a module project.
 *
 * This will automatically detect the tests and run them using the Drupal code
 * within the vendor directory.
 *
 * Copied from drupal/core/tests/bootstrap.php.
 */

use Drupal\Component\Assertion\Handle;
use Drupal\TestTools\PhpUnitCompatibility\PhpUnit8\ClassWriter;

/**
 * Get the root directory of the current project.
 *
 * @return string
 *   The root directory.
 */
function drupal_phpunit_root_dir(): string
{
    return dirname(__FILE__, 7);
}

/**
 * Finds all valid extension directories recursively within a given directory.
 *
 * @param string $scanDirectory
 *   The directory that should be recursively scanned.
 *
 * @return array
 *   An associative array of extension directories found within the scanned
 *   directory, keyed by extension name.
 */
function drupal_phpunit_find_extension_directories($scanDirectory): array
{
    $extensions = [];
    $dirs = new \RecursiveIteratorIterator(
        new \RecursiveDirectoryIterator(
            $scanDirectory,
            \RecursiveDirectoryIterator::FOLLOW_SYMLINKS
        )
    );

    foreach ($dirs as $dir) {
        if (strpos($dir->getPathname(), '.info.yml') !== false) {
            // Cut off ".info.yml" from the filename for use as the extension name. We
            // use getRealPath() so that we can scan extensions represented by
            // directory aliases.
            $extensions[substr($dir->getFilename(), 0, -9)] = $dir
                ->getPathInfo()
                ->getRealPath();
        }
    }

    return $extensions;
}

/**
 * Returns directories under which contributed extensions may exist.
 *
 * @return array
 *   An array of directories under which contributed extensions may exist.
 */
function drupal_phpunit_contrib_extension_directory_roots()
{
    return [
        drupal_phpunit_root_dir() . '/',
        drupal_phpunit_root_dir() . '/vendor/drupal/core/modules',
    ];
}

/**
 * Registers the namespace for each extension directory with the autoloader.
 *
 * @param array $dirs
 *   An associative array of extension directories, keyed by extension name.
 *
 * @return array
 *   An associative array of extension directories, keyed by their namespace.
 */
function drupal_phpunit_get_extension_namespaces(array $dirs)
{
    $suiteNames = ['Unit', 'Kernel', 'Functional', 'FunctionalJavascript'];
    $namespaces = [];
    foreach ($dirs as $extension => $dir) {
        if (is_dir($dir . '/src')) {
            // Register the PSR-4 directory for module-provided classes.
            $namespaces['Drupal\\' . $extension . '\\'][] = $dir . '/src';
        }

        $testDir = $dir . '/tests/src';
        if (!is_dir($testDir)) {
            continue;
        }

        foreach ($suiteNames as $suiteName) {
            $suiteDir = $testDir . '/' . $suiteName;
            if (is_dir($suiteDir)) {
                // Register the PSR-4 directory for PHPUnit-based suites.
                $namespaces['Drupal\\Tests\\' . $extension . '\\' . $suiteName . '\\'][] = $suiteDir;
            }
        }

        // Extensions can have a \Drupal\extension\Traits namespace for
        // cross-suite trait code.
        $traitDir = $testDir . '/Traits';
        if (is_dir($traitDir)) {
            $namespaces['Drupal\\Tests\\' . $extension . '\\Traits\\'][] = $traitDir;
        }
    }

    return $namespaces;
}

/**
 * Populate class loader with additional namespaces for tests.
 *
 * We run this in a function to avoid setting the class loader to a global
 * that can change. This change can cause unpredictable false positives for
 * phpunit's global state change watcher. The class loader can be retrieved from
 * composer at any time by requiring autoload.php.
 */
function drupal_phpunit_populate_class_loader()
{
    /** @var \Composer\Autoload\ClassLoader $loader */
    $loader = require drupal_phpunit_root_dir() . '/vendor/autoload.php';

    $dir = drupal_phpunit_root_dir() . '/vendor/drupal/core/tests';

    // Start with classes in known locations.
    $loader->add('Drupal\\Tests', $dir);
    $loader->add('Drupal\\KernelTests', $dir);
    $loader->add('Drupal\\FunctionalTests', $dir);
    $loader->add('Drupal\\FunctionalJavascriptTests', $dir);
    $loader->add('Drupal\\BuildTests', $dir);
    $loader->add('Drupal\\TestTools', $dir);

    if (!isset($GLOBALS['namespaces'])) {
        // Scan for arbitrary extension namespaces from core and contrib.
        $extensionRoots = drupal_phpunit_contrib_extension_directory_roots();

        $dirs = array_map(
            'drupal_phpunit_find_extension_directories',
            $extensionRoots
        );
        $dirs = array_reduce($dirs, 'array_merge', []);
        $GLOBALS['namespaces'] = drupal_phpunit_get_extension_namespaces($dirs);
    }

    foreach ($GLOBALS['namespaces'] as $prefix => $paths) {
        $loader->addPsr4($prefix, $paths);
    }

    // Ensure we have a valid TestCase class.
    if (class_exists('Drupal\TestTools\PhpUnitCompatibility\PhpUnit8\ClassWriter')) {
        ClassWriter::mutateTestBase($loader);
    }

    return $loader;
}

// Do class loader population.
drupal_phpunit_populate_class_loader();

// Set sane locale settings, to ensure consistent string, dates, times and
// numbers handling.
// @see \Drupal\Core\DrupalKernel::bootEnvironment()
setlocale(LC_ALL, 'C');

// Set the default timezone. While this doesn't cause any tests to fail, PHP
// complains if 'date.timezone' is not set in php.ini. The Australia/Sydney
// timezone is chosen so all tests are run using an edge case scenario (UTC+10
// and DST). This choice is made to prevent timezone related regressions and
// reduce the fragility of the testing system in general.
date_default_timezone_set('Australia/Sydney');

// Runtime assertions. PHPUnit follows the php.ini assert.active setting for
// runtime assertions. By default this setting is on. Here we make a call to
// make PHP 5 and 7 handle assertion failures the same way, but this call does
// not turn runtime assertions on if they weren't on already.
Handle::register();
