<?php

namespace Digipolisgent\QA\Drupal;

use Digipolisgent\QA\Drupal\EventSubscriber\GrumphpEventSubscriber;
use GrumPHP\Extension\ExtensionInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class GrumPHPExtension implements ExtensionInterface
{
    /**
     * @inheritDoc
     */
    public function load(ContainerBuilder $container)
    {
        /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $event_dispatcher */
        $event_dispatcher = $container->get('Symfony\Component\EventDispatcher\EventDispatcher');
        $event_dispatcher->addSubscriber(new GrumphpEventSubscriber());
    }
}
