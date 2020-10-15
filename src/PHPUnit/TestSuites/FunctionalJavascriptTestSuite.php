<?php

namespace Digipolisgent\QA\Drupal\PHPUnit\TestSuites;

/**
 * Discovers tests for the functional-javascript test suite.
 */
class FunctionalJavascriptTestSuite extends TestSuiteBase
{
    /**
     * Factory method which loads up a suite with all functional-javascript tests.
     *
     * @return static The test suite.
     */
    public static function suite()
    {
        return new static('functional-javascript', 'FunctionalJavascript');
    }
}
