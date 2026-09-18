<?php

/**
 * @file
 * Bootstrap for PHPUnit tests run within a Drupal extension project.
 *
 * This file mirrors the relevant parts of Drupal core's PHPUnit bootstrap while
 * locating core in Composer or scaffolded layouts and discovering the
 * extension under test from the package root.
 */

declare(strict_types=1);

use Composer\Autoload\ClassLoader;
use Drupal\TestTools\ErrorHandler\BootstrapErrorHandler;
use Drupal\TestTools\Extension\DeprecationBridge\DeprecationHandler;
use PHPUnit\Runner\ErrorHandler as PhpUnitErrorHandler;
use PHPUnit\TextUI\Configuration\Registry as PhpunitConfigurationRegistry;
use Symfony\Component\ErrorHandler\DebugClassLoader;

/**
 * Gets the root directory of the extension project.
 *
 * @return string
 *   The absolute project root.
 */
function drupal_phpunit_root_dir(): string {
  $configuredRoot = getenv('QA_DRUPAL_PHPUNIT_ROOT');
  if ($configuredRoot !== FALSE && is_file($configuredRoot . '/vendor/autoload.php')) {
    return $configuredRoot;
  }

  $consumerRoot = dirname(__DIR__, 6);
  if (is_file($consumerRoot . '/vendor/autoload.php')) {
    return $consumerRoot;
  }

  // Allow maintainers to smoke-test this bootstrap from the package checkout.
  // Consumer projects use the installed vendor path handled above.
  return dirname(__DIR__, 3);
}

/**
 * Gets the installed Drupal core directory.
 *
 * Extension repositories install core below vendor, while Drupal site
 * projects commonly scaffold it to web/core.
 *
 * @return string
 *   The absolute Drupal core directory.
 */
function drupal_phpunit_core_dir(): string {
  $root = drupal_phpunit_root_dir();
  foreach ([$root . '/vendor/drupal/core', $root . '/web/core'] as $core) {
    if (is_dir($core . '/tests')) {
      return $core;
    }
  }

  throw new RuntimeException('Unable to locate Drupal core PHPUnit tests.');
}

/**
 * Finds all valid extension directories recursively below a directory.
 *
 * @param string $scanDirectory
 *   The directory that should be recursively scanned.
 *
 * @return array<string, string>
 *   Extension directories keyed by extension name.
 */
function drupal_phpunit_find_extension_directories(string $scanDirectory): array {
  $extensions = [];
  $iterator = new RecursiveCallbackFilterIterator(
    new RecursiveDirectoryIterator(
      $scanDirectory,
      RecursiveDirectoryIterator::SKIP_DOTS
    ),
    static function (SplFileInfo $file): bool {
      return !$file->isDir()
        || !in_array($file->getFilename(), ['.git', 'node_modules', 'vendor'], TRUE);
    }
  );
  $directories = new RecursiveIteratorIterator(
    $iterator
  );

  foreach ($directories as $directory) {
    if (!str_ends_with($directory->getFilename(), '.info.yml')) {
      continue;
    }

    $extensionDirectory = $directory->getPathInfo()->getRealPath();
    if ($extensionDirectory !== FALSE) {
      $extensions[substr($directory->getFilename(), 0, -9)] = $extensionDirectory;
    }
  }

  return $extensions;
}

/**
 * Gets directories that can contain the extension and Drupal core extensions.
 *
 * @return string[]
 *   Directories to scan for extensions.
 */
function drupal_phpunit_contrib_extension_directory_roots(): array {
  $root = drupal_phpunit_root_dir();
  $core = drupal_phpunit_core_dir();

  return array_filter([
    $root,
    $core . '/modules',
    $core . '/profiles',
    $core . '/themes',
  ], 'is_dir');
}

/**
 * Builds PSR-4 namespace mappings for discovered Drupal extensions.
 *
 * @param array<string, string> $directories
 *   Extension directories keyed by extension name.
 *
 * @return array<string, string[]>
 *   Extension source and test directories keyed by namespace.
 */
function drupal_phpunit_get_extension_namespaces(array $directories): array {
  $namespaces = [];

  foreach ($directories as $extension => $directory) {
    if (is_dir($directory . '/src')) {
      $namespaces['Drupal\\' . $extension . '\\'][] = $directory . '/src';
    }

    if (is_dir($directory . '/tests/src')) {
      $namespaces['Drupal\\Tests\\' . $extension . '\\'][] = $directory . '/tests/src';
    }
  }

  return $namespaces;
}

if (!defined('PHPUNIT_COMPOSER_INSTALL')) {
  define('PHPUNIT_COMPOSER_INSTALL', drupal_phpunit_root_dir() . '/vendor/autoload.php');
}

/**
 * Populates Composer's class loader with Drupal test namespaces.
 *
 * @return \Composer\Autoload\ClassLoader
 *   The populated Composer class loader.
 */
function drupal_phpunit_populate_class_loader(): ClassLoader {
  /** @var \Composer\Autoload\ClassLoader $loader */
  $loader = require drupal_phpunit_root_dir() . '/vendor/autoload.php';
  $coreTests = drupal_phpunit_core_dir() . '/tests';

  foreach ([
    'Drupal\\BuildTests',
    'Drupal\\Tests',
    'Drupal\\TestSite',
    'Drupal\\KernelTests',
    'Drupal\\FunctionalTests',
    'Drupal\\FunctionalJavascriptTests',
    'Drupal\\TestTools',
  ] as $namespace) {
    $loader->add($namespace, $coreTests);
  }

  if (!isset($GLOBALS['namespaces'])) {
    $directories = array_map(
          'drupal_phpunit_find_extension_directories',
          drupal_phpunit_contrib_extension_directory_roots()
      );
    $extensionDirectories = array_reduce($directories, 'array_merge', []);
    $GLOBALS['namespaces'] = drupal_phpunit_get_extension_namespaces($extensionDirectories);
  }

  foreach ($GLOBALS['namespaces'] as $prefix => $paths) {
    $loader->addPsr4($prefix, $paths);
  }

  return $loader;
}

drupal_phpunit_populate_class_loader();

if (
    class_exists('Drupal\\Tests\\DocumentElement')
    && !class_exists('Behat\\Mink\\Element\\DocumentElement', FALSE)
) {
  class_alias('Drupal\\Tests\\DocumentElement', 'Behat\\Mink\\Element\\DocumentElement');
}

setlocale(LC_ALL, 'C.UTF-8', 'C');
mb_internal_encoding('utf-8');
mb_language('uni');
date_default_timezone_set('Australia/Sydney');

// Drupal 12 initializes deprecation handling from PHPUnit's parsed extension
// configuration. Drupal 11.1 uses the older explicit initialization sequence.
if (method_exists(DeprecationHandler::class, 'preBootstrap')) {
  try {
    DeprecationHandler::preBootstrap(PhpunitConfigurationRegistry::get());
  }
  catch (AssertionError) {
    // PHPUnit's configuration is unavailable when this file is run alone.
  }
}
elseif (
    method_exists(DeprecationHandler::class, 'getConfiguration')
    && ($configuration = DeprecationHandler::getConfiguration())
) {
  DeprecationHandler::init($configuration['ignoreFile'] ?? NULL);

  if (class_exists(BootstrapErrorHandler::class) && class_exists(PhpUnitErrorHandler::class)) {
    try {
      set_error_handler(new BootstrapErrorHandler(PhpUnitErrorHandler::instance()));
    }
    catch (AssertionError) {
      // PHPUnit has no parsed configuration during standalone smoke tests.
    }
  }

  if (class_exists(DebugClassLoader::class)) {
    DebugClassLoader::enable();
  }
}
