<?php

namespace Digipolisgent\QA\Drupal\Behat\Context;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use PHPUnit\Framework\Assert;

/**
 * Provides pre-built step definitions for interacting with URL & URI.
 */
class UriContext extends RawDrupalContext
{
    /**
     * Checks, that a link points to a specific URI.
     *
     * The link is identified by id|title|alt|text.
     *
     * Example: Then the "Home" link should point to "/"
     * Example: And the "Read more" link should point to "/page/detail?test=1233"
     *
     * @Then the :link link should point to :uri
     */
    public function assertLinkUri($link, $uri)
    {
        $link_clean = str_replace('\\"', '"', $link);
        $link_found = $this
            ->getSession()
            ->getPage()
            ->findLink($link_clean);

        Assert::assertNotEmpty(
            $link_found,
            sprintf('Can\'t find link "%s".', $link)
        );

        $href = $link_found->getAttribute('href');
        Assert::assertEquals(
            $uri,
            $href,
            sprintf('The link href "%s" is not equal to "%s"', $href, $uri)
        );
    }

    /**
     * Checks that the current URI matches the given path with fragment and query.
     *
     * Example: Then the current full URI should be "/foo?bar=test"
     *
     * @then the current URI should be :uri
     */
    public function assertCurrentUri($uri)
    {
        $current_uri = $this->getCurrentUri();

        Assert::assertEquals(
            $uri,
            $current_uri,
            sprintf('The uri "%s" is not equal to current uri "%uri".',
                $uri,
                $current_uri
            )
        );
    }

    /**
     * Get the base URL of the site.
     *
     * @return string The base URL of the site.
     */
    public function getBaseUrl()
    {
        return $this->getMinkParameter('base_url');
    }

    /**
     * Get the URL of the current page.
     *
     * @return string The URL of the current page.
     */
    public function getCurrentUrl()
    {
        return $this->getSession()->getCurrentUrl();
    }

    /**
     * Get the current URI without the base URL.
     *
     * @return string The current URI.
     */
    public function getCurrentUri()
    {
        return str_replace(
            rtrim($this->getBaseUrl(), '/'),
            '',
            $this->getCurrentUrl()
        );
    }
}
