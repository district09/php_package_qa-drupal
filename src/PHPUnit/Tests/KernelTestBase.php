<?php

namespace Digipolisgent\QA\Drupal\PHPUnit\Tests;

use Drupal\KernelTests\KernelTestBase as DrupalKernelTestBase;

/**
 * Base class for kernel tests.
 */
abstract class KernelTestBase extends DrupalKernelTestBase
{

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass() {
        $cwd = getcwd();

        parent::setUpBeforeClass();

        // Change back to initial working directory so PHPUnit can find its configuration file.
        chdir($cwd);
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        // Change back to the Drupal root.
        chdir(static::getDrupalRoot());

        parent::setUp();
    }
}
