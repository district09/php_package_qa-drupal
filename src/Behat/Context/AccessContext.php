<?php

namespace Digipolisgent\QA\Drupal\Behat\Context;

use Drupal\DrupalExtension\Context\RawDrupalContext;

/**
 * Defines generic step definitions.
 */
class AccessContext extends RawDrupalContext
{
    /**
     * Checks that a 200.
     *
     * @Then I should have access to the page
     */
    public function assertAccess()
    {
        $this->assertSession()->statusCodeEquals(200);
    }

    /**
     * Checks that a 403 Access Denied error occurred.
     *
     * @Then I should get an access denied error
     */
    public function assertAccessDenied()
    {
        $this->assertSession()->statusCodeEquals(403);
    }
}
