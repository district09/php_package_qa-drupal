<?php

namespace Digipolisgent\QA\Drupal\PHPUnit\Tests;

use Drupal\KernelTests\KernelTestBase as DrupalKernelTestBase;

/**
 * Base class for kernel tests.
 */
abstract class KernelTestBase extends DrupalKernelTestBase
{

    /**
     * The current working directory.
     *
     * @var string
     */
    protected static $cwd;

    /**
     * {@inheritdoc}
     */
    public static function setUpBeforeClass() {
        static::$cwd = getcwd();

        parent::setUpBeforeClass();

        // Change back to initial working directory so PHPUnit can find its configuration file.
        chdir(static::$cwd);
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

    /**
     * {@inheritdoc}
     */
    protected function enableModules(array $modules)
    {
        $module_names = [];
        $modules_paths = [];

        // Allow passing the module name as key and its path as value.
        foreach ($modules as $key => $value) {
            if (is_numeric($key)) {
                $module_names[] = $value;
                continue;
            }

            $module_names[] = $key;
            $modules_paths[$key] = static::$cwd . '/' . $value;
        }

        // Enable the modules that were specified with a custom path.
        if ($modules_paths) {
            $module_handler = $this->container->get('module_handler');
            $active_storage = $this->container->get('config.storage');
            $extension_config = $active_storage->read('core.extension');

            foreach ($modules_paths as $module => $path) {
                // Skip existing modules.
                if ($module_handler->moduleExists($module)) {
                    continue;
                }

                // Add the module.
                $module_handler->addModule($module, $path);

                // Maintain the list of enabled modules in configuration.
                $extension_config['module'][$module] = 0;
            }

            // Write directly to active storage to avoid early instantiation of
            // the event dispatcher which can prevent modules from registering events.
            $active_storage->write('core.extension', $extension_config);
        }

        parent::enableModules($module_names);
    }
}
