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
use Symfony\Component\HttpKernel\Event\ViewEvent;

class RestKernelViewSubscriberSpec extends ObjectBehavior
{
    public function let(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        ResponseTagger $tagHandler
    ) {
        $request->attributes = $attributes;
        $event->getRequest()->willReturn($request);

        $this->beConstructedWith($tagHandler);
    }

    public function it_does_nothing_on_uncachable_methods(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes
    ) {
        $request->isMethodCacheable()->willReturn(false);

        $attributes->get(new AnyValueToken())->shouldNotBecalled();
        $event->getControllerResult()->shouldNotBecalled();
        $event->setControllerResult()->shouldNotBecalled();

        $this->tagUIRestResult($event);
    }

    public function it_does_nothing_on_non_rest_requests(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(false);

        $event->getControllerResult()->shouldNotBecalled();
        $event->setControllerResult()->shouldNotBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * Section
     */
    public function it_writes_tags_on_section(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        Section $restValue,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $restValue->beConstructedWith([['id' => 5]]);
        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(['s5'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * ContentType
     */
    public function it_does_nothing_on_content_type_draft(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentType $restValue,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $restValue->beConstructedWith([['status' => ContentType::STATUS_DRAFT]]);
        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(new AnyValueToken())->shouldNotBecalled();
        $event->setControllerResult()->shouldNotBecalled();

        $this->tagUIRestResult($event);
    }

    public function it_writes_tags_on_content_type_defined(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentType $restValue,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $restValue->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);
        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(['t4'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * RestContentType
     */
    public function it_does_nothing_on_rest_content_type_draft(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        RestContentType $restValue,
        ContentType $contentType,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentType->beConstructedWith([['status' => ContentType::STATUS_DRAFT]]);
        $restValue->contentType = $contentType;
        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(new AnyValueToken())->shouldNotBecalled();
        $event->setControllerResult()->shouldNotBecalled();

        $this->tagUIRestResult($event);
    }

    public function it_writes_tags_on_rest_content_type_defined(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        RestContentType $restValue,
        ContentType $contentType,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentType->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);
        $restValue->contentType = $contentType;
        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(['t4'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * ContentTypeGroupRefList
     */
    public function it_does_nothing_on_rest_content_type_group_ref_draft(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupRefList $restValue,
        ContentType $contentType,
        ContentTypeGroup $contentTypeGroup,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentType->beConstructedWith([['status' => ContentType::STATUS_DRAFT]]);
        $restValue->contentType = $contentType;
        $restValue->contentTypeGroups = [$contentTypeGroup];

        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(new AnyValueToken())->shouldNotBecalled();
        $event->setControllerResult()->shouldNotBecalled();

        $this->tagUIRestResult($event);
    }

    public function it_writes_tags_on_rest_content_type_group_ref_defined(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupRefList $restValue,
        ContentType $contentType,
        ContentTypeGroup $contentTypeGroup,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentType->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);
        $restValue->contentType = $contentType;

        $contentTypeGroup->beConstructedWith([['id' => 2]]);
        $restValue->contentTypeGroups = [$contentTypeGroup];

        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(['t4', 'tg2'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * ContentTypeGroupList
     */
    public function it_writes_tags_on_rest_content_type_group_list(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupList $restValue,
        ContentTypeGroup $contentTypeGroup,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentTypeGroup->beConstructedWith([['id' => 2]]);
        $restValue->contentTypeGroups = [$contentTypeGroup];
        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(['tg2'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * VersionList
     */
    public function it_writes_tags_on_rest_version_list(
        ViewEvent $event,
        Request $request,
        ParameterBag $attributes,
        VersionList $restValue,
        VersionInfo $versionInfo,
        ContentInfo $contentInfo,
        ResponseTagger $tagHandler
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentInfo->beConstructedWith([['id' => 33]]);
        $versionInfo->beConstructedWith([['contentInfo' => $contentInfo]]);
        $restValue->versions = [$versionInfo];
        $event->getControllerResult()->willReturn($restValue);

        $tagHandler->addTags(['c33', 'cv33'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }
}
