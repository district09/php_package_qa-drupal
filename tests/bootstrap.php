<?php

/**
 * @file
 * Loads package and isolated GrumPHP classes for the unit test suite.
 */

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

// GrumPHP Shim isolates GrumPHP inside its PHAR, so load that autoloader when
// unit tests need to construct GrumPHP task types used by this package.
require 'phar://' . dirname(__DIR__) . '/vendor/phpro/grumphp-shim/grumphp.phar/vendor/autoload.php';
