<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace spec\EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\Core\Base\Exceptions\NotFoundException;
use eZ\Publish\Core\Repository\Repository;
use eZ\Publish\Core\Repository\Values\Content\Location;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\AnyValueToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use EzSystems\PlatformHttpCacheBundle\Handler\TagHandlerInterface;

class XLocationIdResponseSubscriberSpec extends ObjectBehavior
{
    public function let(
        FilterResponseEvent $event,
        Response $response,
        TagHandlerInterface $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders
    ) {
        $response->headers = $responseHeaders;
        $event->getResponse()->willReturn($response);

        $this->beConstructedWith($tagHandler, $repository);
    }

    public function it_does_not_rewrite_header_if_there_is_none(
        FilterResponseEvent $event,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(false);
        $responseHeaders->set()->shouldNotBecalled();

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_with_location_info(
        FilterResponseEvent $event,
        Response $response,
        TagHandlerInterface $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123');

        $repository->sudo(new AnyValueToken())->willReturn(
            new Location([
                'id' => 123,
                'parentLocationId' => 2,
                'pathString' => '/1/2/123/',
                'contentInfo' => new ContentInfo(['id' => 101, 'contentTypeId' => 3, 'mainLocationId' => 120])
            ])
        );

        $tagHandler->addTags([
            'location-123',
            'parent-2',
            'path-1',
            'path-2',
            'path-123',
            'content-101',
            'content-type-3',
            'location-120',
        ])->shouldBecalled();
        $responseHeaders->remove('X-Location-Id')->shouldBecalled();

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_on_not_found_location(
        FilterResponseEvent $event,
        Response $response,
        TagHandlerInterface $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123');

        $repository->sudo(new AnyValueToken())->willThrow(new NotFoundException('id', 123));

        $tagHandler->addTags(['location-123', 'path-123'])->shouldBecalled();
        $responseHeaders->remove('X-Location-Id')->shouldBecalled();

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_also_in_unofficial_plural_form_and_merges_exisitng_value(
        FilterResponseEvent $event,
        Response $response,
        TagHandlerInterface $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123,34');

        $repository->sudo(new AnyValueToken())->willThrow(new NotFoundException('id', 123));

        $tagHandler->addTags(['location-123', 'path-123', 'location-34', 'path-34'])->shouldBecalled();
        $responseHeaders->remove('X-Location-Id')->shouldBecalled();

        $this->rewriteCacheHeader($event);
    }
}
