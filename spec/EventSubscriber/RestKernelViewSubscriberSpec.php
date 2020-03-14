<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace spec\EzSystems\PlatformHttpCacheBundle\EventSubscriber;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Section;
use eZ\Publish\API\Repository\Values\ContentType\ContentType;
use eZ\Publish\API\Repository\Values\ContentType\ContentTypeGroup;
use \eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use EzSystems\EzPlatformRest\Server\Values\CachedValue;
use EzSystems\EzPlatformRest\Server\Values\ContentTypeGroupList;
use EzSystems\EzPlatformRest\Server\Values\ContentTypeGroupRefList;
use EzSystems\EzPlatformRest\Server\Values\RestContentType;
use EzSystems\EzPlatformRest\Server\Values\VersionList;
use FOS\HttpCache\ResponseTagger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\AnyValueToken;
use Prophecy\Argument\Token\TypeToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RestKernelViewSubscriberSpec extends ObjectBehavior
{
    public function let(
        Request $request,
        ParameterBag $attributes,
        ResponseTagger $tagHandler
    ) {
        $request->attributes = $attributes;

        $this->beConstructedWith($tagHandler);
    }

    public function it_does_nothing_on_uncachable_methods(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ParameterBag $attributes
    ) {
        $request->isMethodCacheable()->willReturn(false);
        $attributes->get(new AnyValueToken())->shouldNotBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    public function it_does_nothing_on_non_rest_requests(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ParameterBag $attributes
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(false);

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $response->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    /**
     * Section
     */
    public function it_writes_tags_on_section(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        Section $restValue,
        ResponseTagger $tagHandler
    ) {
        $restValue->beConstructedWith([['id' => 5]]);

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(['s5'])->shouldBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    /**
     * ContentType
     */
    public function it_does_nothing_on_content_type_draft(
        HttpKernelInterface $kernel,
        Request $request,
        Response $response,
        ParameterBag $attributes,
        ContentType $restValue,
        ResponseTagger $tagHandler
    ) {
        $restValue->beConstructedWith([['status' => ContentType::STATUS_DRAFT]]);

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(new AnyValueToken())->shouldNotBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    public function it_writes_tags_on_content_type_defined(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        ContentType $restValue,
        ResponseTagger $tagHandler
    ) {
        $restValue->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(['t4'])->shouldBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    /**
     * RestContentType
     */
    public function it_does_nothing_on_rest_content_type_draft(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        RestContentType $restValue,
        ContentType $contentType,
        ResponseTagger $tagHandler
    ) {
        $contentType->beConstructedWith([['status' => ContentType::STATUS_DRAFT]]);
        $restValue->contentType = $contentType;

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(new AnyValueToken())->shouldNotBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    public function it_writes_tags_on_rest_content_type_defined(
        HttpKernelInterface $kernel,
        Request $request,        ParameterBag $attributes,
        RestContentType $restValue,
        ContentType $contentType,
        ResponseTagger $tagHandler
    ) {
        $contentType->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);
        $restValue->contentType = $contentType;

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(['t4'])->shouldBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    /**
     * ContentTypeGroupRefList
     */
    public function it_does_nothing_on_rest_content_type_group_ref_draft(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupRefList $restValue,
        ContentType $contentType,
        ContentTypeGroup $contentTypeGroup,
        ResponseTagger $tagHandler
    ) {
        $contentType->beConstructedWith([['status' => ContentType::STATUS_DRAFT]]);
        $restValue->contentType = $contentType;
        $restValue->contentTypeGroups = [$contentTypeGroup];

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(new AnyValueToken())->shouldNotBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    public function it_writes_tags_on_rest_content_type_group_ref_defined(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupRefList $restValue,
        ContentType $contentType,
        ContentTypeGroup $contentTypeGroup,
        ResponseTagger $tagHandler
    ) {
        $contentType->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);
        $restValue->contentType = $contentType;

        $contentTypeGroup->beConstructedWith([['id' => 2]]);
        $restValue->contentTypeGroups = [$contentTypeGroup];

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(['t4', 'tg2'])->shouldBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    /**
     * ContentTypeGroupList
     */
    public function it_writes_tags_on_rest_content_type_group_list(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupList $restValue,
        ContentTypeGroup $contentTypeGroup,
        ResponseTagger $tagHandler
    ) {
        $contentTypeGroup->beConstructedWith([['id' => 2]]);
        $restValue->contentTypeGroups = [$contentTypeGroup];

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(['tg2'])->shouldBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }

    /**
     * VersionList
     */
    public function it_writes_tags_on_rest_version_list(
        HttpKernelInterface $kernel,
        Request $request,
        ParameterBag $attributes,
        VersionList $restValue,
        VersionInfo $versionInfo,
        ContentInfo $contentInfo,
        ResponseTagger $tagHandler
    ) {
        $contentInfo->beConstructedWith([['id' => 33]]);
        $versionInfo->beConstructedWith([['contentInfo' => $contentInfo]]);
        $restValue->versions = [$versionInfo];

        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $tagHandler->addTags(['c33', 'cv33'])->shouldBeCalled();

        $event = new ViewEvent(
            $kernel->getWrappedObject(),
            $request->getWrappedObject(),
            HttpKernelInterface::MASTER_REQUEST,
            $restValue->getWrappedObject()
        );

        $this->tagUIRestResult($event);
    }
}
