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
use FOS\HttpCache\ResponseTagger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\AnyValueToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class XLocationIdResponseSubscriberSpec extends ObjectBehavior
{
    public function let(
        Response $response,
        ResponseTagger $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders
    ) {
        $response->headers = $responseHeaders;

        $this->beConstructedWith($tagHandler, $repository);
    }

    public function it_does_not_rewrite_header_if_there_is_none(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(false);
        $responseHeaders->set()->shouldNotBecalled();

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_with_location_info(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseTagger $tagHandler,
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
            'l123',
            'pl2',
            'p1',
            'p2',
            'p123',
            'c101',
            'ct3',
            'l120',
        ])->shouldBecalled();
        $responseHeaders->remove('X-Location-Id')->shouldBecalled();

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_on_not_found_location(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseTagger $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123');

        $repository->sudo(new AnyValueToken())->willThrow(new NotFoundException('id', 123));

        $tagHandler->addTags(['l123', 'p123'])->shouldBecalled();
        $responseHeaders->remove('X-Location-Id')->shouldBecalled();

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $this->rewriteCacheHeader($event);
    }

    public function it_rewrite_header_also_in_unofficial_plural_form_and_merges_exisitng_value(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ResponseTagger $tagHandler,
        Repository $repository,
        ResponseHeaderBag $responseHeaders
    ) {
        $responseHeaders->has('X-Location-Id')->willReturn(true);
        $responseHeaders->get('X-Location-Id')->willReturn('123,34');

        $repository->sudo(new AnyValueToken())->willThrow(new NotFoundException('id', 123));

        $tagHandler->addTags(['l123', 'p123', 'l34', 'p34'])->shouldBeCalled();
        $responseHeaders->remove('X-Location-Id')->shouldBeCalled();

        $event = new ResponseEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $this->rewriteCacheHeader($event);
    }
}
