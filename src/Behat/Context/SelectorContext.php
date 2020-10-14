<?php

namespace Digipolisgent\QA\Drupal\Behat\Context;

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Provides pre-built step definitions for interacting with CSS selectors.
 */
class SelectorContext extends RawDrupalContext
{
    /**
     * Checks for text in specific html element.
     *
     * Example: Then I should see "hello world" in the "div#title" element
     * Example: Then I should see "john doe" in the "ul#users > li" element
     *
     * @Then /^(?:|I )should see "(?P<text>.+)" in the "(?P<selector>\w+)" element$/
     *
     * @throws \Exception
     */
    public function assertElementText($text, $selector)
    {
        $page = $this->getSession()->getPage();
        $elements = $page->findAll('css', $selector);

        foreach ($elements as $element) {
            if (stripos($text, $element->getText()) !== false) {
                return;
            }
        }

        throw new \Exception("Text '$text' is not found in the '$selector' element.");
    }

    /**
     * Checks for the absence of text in specific html element.
     *
     * Example: Then I should not see "hello world" in the "div#title" element
     * Example: Then I should not see "john doe" in the "ul#users > li" element
     *
     * @Then /^(?:|I )should not see "(?P<text>.+)" in the "(?P<selector>\w+)" element$/
     *
     * @throws \Exception
     */
    public function notAssertElementText($text, $selector)
    {
        $page = $this->getSession()->getPage();
        $elements = $page->findAll('css', $selector);

        foreach ($elements as $element) {
            if (stripos($text, $element->getText()) !== false) {
                throw new \Exception("Text '$text' is wrongly found in the '$selector' element.");
            }
        }
    }
}
