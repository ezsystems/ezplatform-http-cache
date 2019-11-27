<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use eZ\Publish\Core\MVC\Symfony\View\CachableView;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Configures the Response HTTP cache properties.
 */
class HttpCacheResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger
     */
    private $dispatcherTagger;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator
     */
    private $responseConfigurator;

    public function __construct(ResponseCacheConfigurator $responseConfigurator, ResponseTagger $dispatcherTagger)
    {
        $this->responseConfigurator = $responseConfigurator;
        $this->dispatcherTagger = $dispatcherTagger;
    }

    public static function getSubscribedEvents()
    {
        return [KernelEvents::RESPONSE => ['configureCache', 10]];
    }

    public function configureCache(ResponseEvent $event)
    {
        $view = $event->getRequest()->attributes->get('view');
        if (!$view instanceof CachableView || !$view->isCacheEnabled()) {
            return;
        }

        $response = $event->getResponse();
        $this->responseConfigurator->enableCache($response);
        $this->responseConfigurator->setSharedMaxAge($response);
        $this->dispatcherTagger->tag($view);

        // NB!: FOSHTTPCacheBundle is taking care about writing the tags in own tag handler happening with priority 0
    }
}
