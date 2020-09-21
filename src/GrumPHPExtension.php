<?php

namespace Gent\QA\Drupal;

use Gent\QA\Drupal\EventSubscriber\GrumphpEventSubscriber;
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
        $event_dispatcher = $container->get('symfony\component\eventdispatcher\eventdispatcher');
        $event_dispatcher->addSubscriber(new GrumphpEventSubscriber());
    }
}
