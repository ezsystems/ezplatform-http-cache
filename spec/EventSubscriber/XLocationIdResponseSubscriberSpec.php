<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace spec\EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class XLocationIdResponseSubscriberSpec extends ObjectBehavior
{
    public function let(
        FilterResponseEvent $event,
        Response $response,
        ResponseHeaderBag $responseHeaders
    ) {
        $response->headers = $responseHeaders;
        $event->getResponse()->willReturn($response);

        $this->beConstructedWith('Surrogate-Key');
    }

    public function it_does_not_rewrite_header_if_there_is_none(
        FilterResponseEvent $event,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(false);
        $responseHeaders->set()->shouldNotBecalled();

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_if_there(
        FilterResponseEvent $event,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123');
        $responseHeaders->has('Surrogate-Key')->willReturn(false);

        $responseHeaders->set('Surrogate-Key', ['location-123'])->willReturn(null);
        $responseHeaders->remove('X-Location-Id')->willReturn(null);

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_also_in_unofficial_plural_form_and_merges_exisitng_value(
        FilterResponseEvent $event,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123,34');
        $responseHeaders->has('Surrogate-Key')->willReturn(true);
        $responseHeaders->get('Surrogate-Key', null, false)->willReturn(['content-44']);

        $responseHeaders->set('Surrogate-Key', ['content-44', 'location-123', 'location-34'])->willReturn(null);
        $responseHeaders->remove('X-Location-Id')->willReturn(null);

        $this->rewriteCacheHeader($event);
    }
}
