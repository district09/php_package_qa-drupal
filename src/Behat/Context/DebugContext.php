<?php

namespace Gent\QA\Drupal\Behat\Context;

use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Testwork\Tester\Result\TestResult;

/**
 * Context to dump steps or when an error occurred.
 */
class DebugContext extends RawMinkContext
{
    /**
     * The dump directory.
     *
     * @var string
     */
    private $dumpDirectory;

    /**
     * Dump the HTML.
     *
     * @var bool
     */
    private $dumpToHtml;

    /**
     * Dump the screenshots.
     *
     * @var bool
     */
    private $dumpToScreenshot;

    /**
     * Dump all steps.
     *
     * @var bool
     */
    private $dumpAllSteps;

    /**
     * Class constructor.
     *
     * @param string $path The path were to save the dumps.
     * @param bool $html Wether to dump the HTML.
     * @param bool $screenshot Wether to create a screenshot.
     * @param bool $allSteps Set to true to dump all steps.
     */
    public function __construct($path, $html = true, $screenshot = true, $all_steps = false)
    {
        $this->dumpToHtml = $html;
        $this->dumpToScreenshot = $screenshot;
        $this->dumpAllSteps = $all_steps;

        if ($path && ((is_dir($path) && is_writable($path)) || (!file_exists($path) && @mkdir($path, 0777, true)))) {
            $this->dumpDirectory = rtrim($path, '/');
        }
    }

    /**
     * @AfterStep
     *
     * Check after each step if a dump of the session step should be dumped.
     *
     * @param \Behat\Behat\Hook\Scope\AfterStepScope $afterStepScope
     */
    public function debugDumpAfterStep(AfterStepScope $afterStepScope)
    {
        if (TestResult::FAILED === $afterStepScope->getTestResult()->getResultCode()) {
            $this->dumpAll();
            return;
        }

        if (!$this->dumpAllSteps) {
            return;
        }

        $this->dumpAll();
    }

    /**
     * Pauses the scenario until the user presses a key. Useful when debugging a scenario.
     *
     * @Then (I )put a breakpoint
     */
    public function iPutABreakpoint()
    {
        fwrite(STDOUT, "\033[s    \033[93m[Breakpoint] Press \033[1;93m[RETURN]\033[0;93m to continue...\033[0m");
        while (fgets(STDIN, 1024) === '') {
        }
        fwrite(STDOUT, "\033[u");
    }

    /**
     * Dump the HTML to the location as defined in the settings.
     *
     * @When I save a HTML dump
     */
    public function iSaveAHtmlDump()
    {
        $fileName = $this->getFileName() . '-forced.html';
        $this->dumpHtml($fileName);
    }

    /**
     * Take a screenshot and save it to location as defined in the settings.
     *
     * @When I take a screenshot
     */
    public function iTakeAScreenShot()
    {
        $fileName = $this->getFileName() . '-forced.png';
        $this->dumpScreenshot($fileName);
    }

    /**
     * Print messages to the console.
     *
     * @param $message
     */
    public function printDebug($message)
    {
        echo $message;
    }

    /**
     * Wrapper to dump in all possible and enabled formats.
     */
    protected function dumpAll()
    {
        $fileName = $this->getFileName();

        if ($this->dumpToHtml) {
            $this->dumpHtml($fileName . '.html');
        }

        if ($this->dumpToScreenshot) {
            $this->dumpScreenshot($fileName . '.png');
        }
    }

    /**
     * Helper function to dump the HTML from the session.
     *
     * @param string $filename
     *   The filename to save the HTML to.
     */
    protected function dumpHtml($filename)
    {
        if (!$filename = $this->getFilePath($filename)) {
            return;
        }

        // Add a header with dump details.
        $html = '<!--' . PHP_EOL;
        $html .= 'HTML dump from BEHAT' . PHP_EOL;
        $html .= 'Date:' . date('Y-m-d H:i:s') . PHP_EOL;
        $html .= 'Url: ' . $this->getSession()->getCurrentUrl() . PHP_EOL;

        // Dump the HTML with absolute URLs.
        $html .= preg_replace(
            '#\"(/)#',
            '"' . rtrim($this->getMinkParameter('base_url'), '/') . '$1',
            $this->getSession()->getPage()->getContent()
        );

        file_put_contents($filename, $html);

        $this->printDebug('HTML saved to: file://' . $filename);
    }

    /**
     * Helper function to take and save the screenshot.
     *
     * @param string $filename
     *   The screenshot filename.
     */
    protected function dumpScreenshot($filename)
    {
        if (!$filename = $this->getFilePath($filename)) {
            return;
        }

        // Only selenium supports screenshots.
        $driver = $this->getMink()->getSession()->getDriver();
        if (!($driver instanceof Selenium2Driver)) {
            return;
        }

        $this->saveScreenshot($filename, $this->dumpDirectory);
        $this->printDebug('Screenshot saved to: file://'. $filename);
    }

    /**
     * Create a unique filename.
     *
     * @return string
     *   The file name.
     */
    protected function getFileName()
    {
        return date('YmdHis') . '_' . uniqid('', true);
    }

    /**
     * Get the file path.
     *
     * @param string $filename
     *   The file name.
     *
     * @return string|false
     *   The file path of false if the dump path doesn't exist.
     */
    protected function getFilePath($filename)
    {
        if (!$this->dumpDirectory) {
            return false;
        }

        return $this->dumpDirectory . DIRECTORY_SEPARATOR . $filename;
    }
}
