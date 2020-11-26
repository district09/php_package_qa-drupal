<?php

namespace Digipolisgent\QA\Drupal\PHPUnit\TestSuites;

/**
 * Discovers tests for the kernel test suite.
 */
class KernelTestSuite extends TestSuiteBase
{
    /**
     * Factory method which loads up a suite with all kernel tests.
     *
     * @return static The test suite.
     */
    public static function suite(): KernelTestSuite
    {
        return new static('kernel', 'Kernel');
    }
}
