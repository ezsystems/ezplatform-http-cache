<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseConfigurator;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ConfigurableResponseCacheConfigurator;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use FOS\HttpCache\Handler\TagHandler;

class ConfigurableResponseCacheConfiguratorSpec extends ObjectBehavior
{
    public function let(
        Response $response,
        ResponseHeaderBag $headers,
        ConfigResolverInterface $configResolver
    ) {
        $response->headers = $headers;
        $this->beConstructedWith($configResolver);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfigurableResponseCacheConfigurator::class);
    }

    public function it_sets_cache_control_to_public_if_viewcache_is_enabled(
        ConfigResolverInterface $configResolver,
        Response $response
    ) {
        $configResolver->getParameter('content.view_cache')->willReturn(true);
        $configResolver->getParameter('content.ttl_cache')->willReturn(false);
        $configResolver->getParameter('content.default_ttl')->willReturn(0);

        $response->setPublic()->willReturn($response);

        $this->beConstructedWith($configResolver);
        $this->enableCache($response);

        $response->setPublic()->shouldHaveBeenCalled();
    }

    public function it_does_not_set_cache_control_if_viewcache_is_disabled(
        ConfigResolverInterface $configResolver,
        Response $response
    ) {
        $configResolver->getParameter('content.view_cache')->willReturn(false);
        $configResolver->getParameter('content.ttl_cache')->willReturn(false);
        $configResolver->getParameter('content.default_ttl')->willReturn(0);

        $this->beConstructedWith($configResolver);
        $this->enableCache($response);

        $response->setPublic()->shouldNotHaveBeenCalled();
    }

    public function it_does_not_set_shared_maxage_if_ttl_cache_is_disabled(
        ConfigResolverInterface $configResolver,
        Response $response
    ) {
        $configResolver->getParameter('content.view_cache')->willReturn(true);
        $configResolver->getParameter('content.ttl_cache')->willReturn(false);
        $configResolver->getParameter('content.default_ttl')->willReturn(30);

        $this->beConstructedWith($configResolver);
        $this->setSharedMaxAge($response);

        $response->setSharedMaxAge(30)->shouldNotHaveBeenCalled();
    }

    public function it_does_not_set_shared_maxage_if_it_is_already_set_in_the_response(
        ConfigResolverInterface $configResolver,
        Response $response,
        ResponseHeaderBag $headers
    ) {
        $configResolver->getParameter('content.view_cache')->willReturn(true);
        $configResolver->getParameter('content.ttl_cache')->willReturn(true);
        $configResolver->getParameter('content.default_ttl')->willReturn(30);

        $this->beConstructedWith($configResolver);
        $headers->hasCacheControlDirective('s-maxage')->willReturn(true);

        $this->setSharedMaxAge($response);

        $response->setSharedMaxAge($response, 30)->shouldNotHaveBeenCalled();
    }

    public function it_sets_shared_maxage(
        ConfigResolverInterface $configResolver,
        Response $response,
        ResponseHeaderBag $headers
    ) {
        $configResolver->getParameter('content.view_cache')->willReturn(true);
        $configResolver->getParameter('content.ttl_cache')->willReturn(true);
        $configResolver->getParameter('content.default_ttl')->willReturn(30);

        $response->setSharedMaxAge(30)->willReturn($response);
        $this->beConstructedWith($configResolver);
        $headers->hasCacheControlDirective('s-maxage')->willReturn(false);

        $this->setSharedMaxAge($response);

        $response->setSharedMaxAge(30)->shouldHaveBeenCalled();
    }
}
