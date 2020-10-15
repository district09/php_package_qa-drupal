<?php

namespace Digipolisgent\QA\Drupal\PHPUnit\TestSuites;

/**
 * Discovers tests for the unit test suite.
 */
class UnitTestSuite extends TestSuiteBase
{
    /**
     * Factory method which loads up a suite with all unit tests.
     *
     * @return static The test suite.
     */
    public static function suite()
    {
        return new static('unit', 'Unit');
    }
}
