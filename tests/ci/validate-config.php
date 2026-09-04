<?php

/**
 * @file
 * Validates the syntax of the distributed QA configuration files.
 */

declare(strict_types=1);

use Nette\Neon\Neon;
use Symfony\Component\Yaml\Yaml;

require dirname(__DIR__, 2) . '/vendor/autoload.php';

$root = dirname(__DIR__, 2);
$patterns = [
  'yaml' => [
    $root . '/.github/workflows/*.yml',
    $root . '/configs/*.yml',
    $root . '/scaffold/*.yml.dist',
  ],
  'neon' => [$root . '/configs/*.neon'],
  'xml' => [
    $root . '/configs/*.xml',
    $root . '/tests/*.xml',
    $root . '/phpunit.xml.dist',
  ],
];

foreach ($patterns['yaml'] as $pattern) {
  foreach (glob($pattern) ?: [] as $file) {
    Yaml::parseFile($file);
  }
}

foreach ($patterns['neon'] as $pattern) {
  foreach (glob($pattern) ?: [] as $file) {
    Neon::decode((string) file_get_contents($file));
  }
}

libxml_use_internal_errors(TRUE);
foreach ($patterns['xml'] as $pattern) {
  foreach (glob($pattern) ?: [] as $file) {
    if (simplexml_load_file($file) === FALSE) {
      $errors = array_map(
            static fn (LibXMLError $error): string => trim($error->message),
            libxml_get_errors()
        );
      throw new RuntimeException(sprintf(
            "Invalid XML in %s:\n%s",
            $file,
            implode("\n", $errors)
        ));
    }
    libxml_clear_errors();
  }
}

fwrite(STDOUT, "Configuration files are valid.\n");
