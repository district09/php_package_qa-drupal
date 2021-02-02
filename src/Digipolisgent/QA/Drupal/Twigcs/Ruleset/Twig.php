<?php

namespace Digipolisgent\QA\Drupal\Twigcs\Ruleset;

use FriendsOfTwig\Twigcs\Ruleset\Official;
use FriendsOfTwig\Twigcs\Ruleset\RulesetInterface;
use NdB\TwigCSA11Y\Ruleset;

/**
 * Twig ruleset.
 *
 * Combines official and wcag rulesets.
 */
class Twig implements RulesetInterface
{
    private $twigMajorVersion;

    public function __construct(int $twigMajorVersion)
    {
        $this->twigMajorVersion = $twigMajorVersion;
    }

    /**
     * {@inheritdoc}
     */
    public function getRules()
    {
        $official = new Official($this->twigMajorVersion);
        $wcag = new Ruleset($this->twigMajorVersion);

        return array_merge($official->getRules(), $wcag->getRules());
    }
}
