<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace spec\EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ResponseCacheConfigurator;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use eZ\Publish\Core\MVC\Symfony\View\CachableView;
use eZ\Publish\Core\MVC\Symfony\View\View;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class HttpCacheResponseSubscriberSpec extends ObjectBehavior
{
    public function let(
        Request $request,
        ParameterBag $requestAttributes,
        ResponseCacheConfigurator $configurator,
        ResponseTagger $dispatcherTagger
    ) {
        $request->attributes = $requestAttributes;

        $this->beConstructedWith($configurator, $dispatcherTagger);
    }

    public function it_does_not_enable_cache_if_the_view_is_not_a_cachableview(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseCacheConfigurator $configurator,
        ParameterBag $requestAttributes,
        View $nonCachableView
    ) {
        $requestAttributes->get('view')->willReturn($nonCachableView);

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $configurator->enableCache()->shouldNotBecalled();

        $this->configureCache($event);
    }

    public function it_does_not_enable_cache_if_it_is_disabled_in_the_view(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseCacheConfigurator $configurator,
        CachableView $view,
        ParameterBag $requestAttributes
    ) {
        $requestAttributes->get('view')->willReturn($view);
        $view->isCacheEnabled()->willReturn(false);

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $configurator->enableCache()->shouldNotBecalled();

        $this->configureCache($event);
    }

    public function it_enables_cache(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseCacheConfigurator $configurator,
        CachableView $view,
        ParameterBag $requestAttributes,
        ResponseTagger $dispatcherTagger
    ) {
        $requestAttributes->get('view')->willReturn($view);
        $view->isCacheEnabled()->willReturn(true);

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $this->configureCache($event);

        $configurator->enableCache(Argument::type(Response::class))->shouldHaveBeenCalled();
        $configurator->setSharedMaxAge(Argument::type(Response::class))->shouldHaveBeenCalled();
        $dispatcherTagger->tag($view)->shouldHaveBeenCalled();
    }
}
