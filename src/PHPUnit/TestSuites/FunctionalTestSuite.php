<?php

namespace Digipolisgent\QA\Drupal\PHPUnit\TestSuites;

/**
 * Discovers tests for the functional test suite.
 */
class FunctionalTestSuite extends TestSuiteBase
{
    /**
     * Factory method which loads up a suite with all functional tests.
     *
     * @return static
     *   The test suite.
     */
    public static function suite()
    {
        return new static('functional', 'Functional');
    }
}
