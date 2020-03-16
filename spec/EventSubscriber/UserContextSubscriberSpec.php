<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use EzSystems\PlatformHttpCacheBundle\EventSubscriber\UserContextSubscriber;
use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Prophecy\Argument\Token\AnyValueToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class UserContextSubscriberSpec extends ObjectBehavior
{
    public function let(
        RepositoryTagPrefix $prefixService,
        Response $response,
        ResponseEvent $event,
        ResponseHeaderBag $responseHeaders
    ) {
        $response->headers = $responseHeaders;
        $event->getResponse()->willReturn($response);

        $this->beConstructedWith($prefixService, 'xkey');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(UserContextSubscriber::class);
    }

    public function it_does_nothing_on_uncachable_methods(
        Response $response,
        ResponseEvent $event,
        ResponseHeaderBag $responseHeaders
    ) {
        $response->isCacheable()->willReturn(false);

        $responseHeaders->get(new AnyValueToken())->shouldNotBecalled();
        $response->getTtl()->shouldNotBecalled();
        $responseHeaders->set(new AnyValueToken(), new AnyValueToken())->shouldNotBecalled();

        $this->tagUserContext($event);
    }

    public function it_does_nothing_on_wrong_content_type(
        Response $response,
        ResponseEvent $event,
        ResponseHeaderBag $responseHeaders
    ) {
        $response->isCacheable()->willReturn(true);
        $responseHeaders->get(Argument::exact('Content-Type'))->willReturn('text/html');

        $response->getTtl()->shouldNotBecalled();
        $responseHeaders->set(new AnyValueToken(), new AnyValueToken())->shouldNotBecalled();

        $this->tagUserContext($event);
    }

    public function it_does_nothing_on_empty_ttl(
        Response $response,
        ResponseEvent $event,
        ResponseHeaderBag $responseHeaders
    ) {
        $response->isCacheable()->willReturn(true);
        $responseHeaders->get(Argument::exact('Content-Type'))->willReturn('application/vnd.fos.user-context-hash');
        $response->getTtl()->willReturn(0);

        $responseHeaders->set(new AnyValueToken(), new AnyValueToken())->shouldNotBecalled();

        $this->tagUserContext($event);
    }

    public function it_tags_response_with_no_prefix(
        Response $response,
        ResponseEvent $event,
        ResponseHeaderBag $responseHeaders,
        RepositoryTagPrefix $prefixService
    ) {
        $response->isCacheable()->willReturn(true);
        $responseHeaders->get(Argument::exact('Content-Type'))->willReturn('application/vnd.fos.user-context-hash');
        $response->getTtl()->willReturn(100);

        $prefixService->getRepositoryPrefix()->willReturn('');
        $responseHeaders->set(Argument::exact('xkey'), Argument::exact('ez-user-context-hash'))->willReturn(null);

        $this->tagUserContext($event);
    }

    public function it_tags_response_with_a_prefix(
        Response $response,
        ResponseEvent $event,
        ResponseHeaderBag $responseHeaders,
        RepositoryTagPrefix $prefixService
    ) {
        $response->isCacheable()->willReturn(true);
        $responseHeaders->get(Argument::exact('Content-Type'))->willReturn('application/vnd.fos.user-context-hash');
        $response->getTtl()->willReturn(100);

        $prefixService->getRepositoryPrefix()->willReturn('1');
        $responseHeaders->set(Argument::exact('xkey'), Argument::exact('1ez-user-context-hash'))->willReturn(null);

        $this->tagUserContext($event);
    }
}
