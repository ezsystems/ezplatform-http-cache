<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseConfigurator;

use EzSystems\PlatformHttpCacheBundle\ResponseConfigurator\ConfigurableResponseCacheConfigurator;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use EzSystems\PlatformHttpCacheBundle\Handler\TagHandlerInterface;

class ConfigurableResponseCacheConfiguratorSpec extends ObjectBehavior
{
    public function let(Response $response, ResponseHeaderBag $headers, TagHandlerInterface $tagHandler)
    {
        $response->headers = $headers;
        $this->beConstructedWith(true, true, 30, $tagHandler);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ConfigurableResponseCacheConfigurator::class);
    }

    public function it_sets_cache_control_to_public_if_viewcache_is_enabled(Response $response, TagHandlerInterface $tagHandler)
    {
        $this->beConstructedWith(true, false, 0, $tagHandler);
        $this->enableCache($response);

        $response->setPublic()->shouldHaveBeenCalled();
    }

    public function it_does_not_set_cache_control_if_viewcache_is_disabled(Response $response, TagHandlerInterface $tagHandler)
    {
        $this->beConstructedWith(false, false, 0, $tagHandler);
        $this->enableCache($response);

        $response->setPublic()->shouldNotHaveBeenCalled();
    }

    public function it_does_not_set_shared_maxage_if_ttl_cache_is_disabled(Response $response, TagHandlerInterface $tagHandler)
    {
        $this->beConstructedWith(true, false, 30, $tagHandler);
        $this->setSharedMaxAge($response);

        $response->setSharedMaxAge(30)->shouldNotHaveBeenCalled();
    }

    public function it_does_not_set_shared_maxage_if_it_is_already_set_in_the_response(Response $response, ResponseHeaderBag $headers, TagHandlerInterface $tagHandler)
    {
        $this->beConstructedWith(true, true, 30, $tagHandler);
        $headers->hasCacheControlDirective('s-maxage')->willReturn(true);

        $this->setSharedMaxAge($response);

        $response->setSharedMaxAge($response, 30)->shouldNotHaveBeenCalled();
    }

    public function it_sets_shared_maxage(Response $response, ResponseHeaderBag $headers, TagHandlerInterface $tagHandler)
    {
        $this->beConstructedWith(true, true, 30, $tagHandler);
        $headers->hasCacheControlDirective('s-maxage')->willReturn(false);

        $this->setSharedMaxAge($response);

        $response->setSharedMaxAge(30)->shouldHaveBeenCalled();
    }

    public function it_does_not_add_tags_if_viewcache_is_disabled(Response $response, ResponseHeaderBag $headers, TagHandlerInterface $tagHandler)
    {
        $this->beConstructedWith(false, false, 0, $tagHandler);
        $this->addTags($response, ['foo-1', 'bar-2']);

        $headers->set('xkey', ['foo-1', 'bar-2'])->shouldNotHaveBeenCalled();
    }

    public function it_adds_tags_to_the_xkey_header(Response $response, ResponseHeaderBag $headers, TagHandlerInterface $tagHandler)
    {
        $this->beConstructedWith(false, false, 0, $tagHandler);
        $this->addTags($response, ['foo-1', 'bar-2']);

        $headers->set('xkey', ['foo-1', 'bar-2'])->shouldNotHaveBeenCalled();
    }
}
