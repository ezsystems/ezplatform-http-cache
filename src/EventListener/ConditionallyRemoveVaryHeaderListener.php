<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ConditionallyRemoveVaryHeaderListener
 * Unfortunately, FOS\HttpCacheBundle\EventListener\UserContextSubscriber will set Vary header on all requests.
 * This event listeners removes the $userIdentifierHeaders headers again in responses to any of the given $routes.
 * For such routes, the controller should instead set the Vary header explicitly.
 */
class ConditionallyRemoveVaryHeaderListener implements EventSubscriberInterface
{
    /**
     * @var string[]
     */
    private $routes;

    /**
     * @var string[]
     */
    private $userIdentifierHeaders;

    /**
     * ConditionallyRemoveVaryHeaderListener constructor.
     *
     * @param array $routes List of routes which will not have default vary headers
     * @param array $userIdentifierHeaders
     */
    public function __construct(array $routes, array $userIdentifierHeaders = ['Cookie', 'Authorization'])
    {
        $this->routes = $routes;
        $this->userIdentifierHeaders = $userIdentifierHeaders;
    }

    /**
     * Remove Vary headers for matched routes.
     *
     * @param ResponseEvent $event
     */
    public function onKernelResponse(ResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST != $event->getRequestType()) {
            return;
        }

        if (!\in_array($event->getRequest()->get('_route'), $this->routes)) {
            return;
        }

        $response = $event->getResponse();
        $varyHeaders = $response->headers->all('vary');

        foreach ($this->userIdentifierHeaders as $removableVary) {
            unset($varyHeaders[array_search(strtolower($removableVary), [$varyHeaders])]);
        }
        $response->setVary($varyHeaders, true);
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::RESPONSE => 'onKernelResponse',
        ];
    }
}
