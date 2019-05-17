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
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\AnyValueToken;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use FOS\HttpCache\Handler\TagHandler;

class XLocationIdResponseSubscriberSpec extends ObjectBehavior
{
    public function let(
        FilterResponseEvent $event,
        Response $response,
        TagHandler $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders,
        TagProviderInterface $tagProvider
    ) {
        $response->headers = $responseHeaders;
        $event->getResponse()->willReturn($response);

        $this->beConstructedWith($tagHandler, $repository, $tagProvider);
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
        TagHandler $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders,
        TagProviderInterface $tagProvider
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123');

        $repository->sudo(new AnyValueToken())->willReturn(
            new Location([
                'id' => 123,
                'parentLocationId' => 2,
                'pathString' => '/1/2/123/',
                'contentInfo' => new ContentInfo(['id' => 101, 'contentTypeId' => 3, 'mainLocationId' => 120]),
            ])
        );

        $tagProvider->getTagForLocationId(123)->willReturn('location-123');
        $tagProvider->getTagForParentId(2)->willReturn('parent-2');
        $tagProvider->getTagForPathId(1)->willReturn('path-1');
        $tagProvider->getTagForPathId(2)->willReturn('path-2');
        $tagProvider->getTagForPathId(123)->willReturn('path-123');
        $tagProvider->getTagForContentId(101)->willReturn('content-101');
        $tagProvider->getTagForContentTypeId(3)->willReturn('content-type-3');
        $tagProvider->getTagForLocationId(120)->willReturn('location-120');

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
        TagHandler $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders,
        TagProviderInterface $tagProvider
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123');

        $repository->sudo(new AnyValueToken())->willThrow(new NotFoundException('id', 123));

        $tagProvider->getTagForLocationId(123)->willReturn('location-123');
        $tagProvider->getTagForPathId(123)->willReturn('path-123');

        $tagHandler->addTags(['location-123', 'path-123'])->shouldBecalled();
        $responseHeaders->remove('X-Location-Id')->shouldBecalled();

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_also_in_unofficial_plural_form_and_merges_exisitng_value(
        FilterResponseEvent $event,
        Response $response,
        TagHandler $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders,
        TagProviderInterface $tagProvider
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123,34');

        $repository->sudo(new AnyValueToken())->willThrow(new NotFoundException('id', 123));

        $tagProvider->getTagForLocationId(123)->willReturn('location-123');
        $tagProvider->getTagForPathId(123)->willReturn('path-123');
        $tagProvider->getTagForLocationId(34)->willReturn('location-34');
        $tagProvider->getTagForPathId(34)->willReturn('path-34');

        $tagHandler->addTags(['location-123', 'path-123', 'location-34', 'path-34'])->shouldBecalled();
        $responseHeaders->remove('X-Location-Id')->shouldBecalled();

        $this->rewriteCacheHeader($event);
    }
}
