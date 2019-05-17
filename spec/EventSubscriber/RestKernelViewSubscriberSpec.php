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
use eZ\Publish\Core\Repository\Values\Content\VersionInfo;
use eZ\Publish\Core\REST\Server\Values\CachedValue;
use eZ\Publish\Core\REST\Server\Values\ContentTypeGroupList;
use eZ\Publish\Core\REST\Server\Values\ContentTypeGroupRefList;
use eZ\Publish\Core\REST\Server\Values\RestContentType;
use eZ\Publish\Core\REST\Server\Values\VersionList;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument\Token\AnyValueToken;
use Prophecy\Argument\Token\TypeToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use FOS\HttpCache\Handler\TagHandler;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class RestKernelViewSubscriberSpec extends ObjectBehavior
{
    public function let(
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        TagHandler $tagHandler,
        TagProviderInterface $tagProvider
    ) {
        $request->attributes = $attributes;
        $event->getRequest()->willReturn($request);

        $this->beConstructedWith($tagHandler, $tagProvider);
    }

    public function it_does_nothing_on_uncachable_methods(
        GetResponseForControllerResultEvent $event,
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
        GetResponseForControllerResultEvent $event,
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
     * Section.
     */
    public function it_writes_tags_on_section(
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        Section $restValue,
        TagHandler $tagHandler,
        TagProviderInterface $tagProvider
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $restValue->beConstructedWith([['id' => 5]]);
        $event->getControllerResult()->willReturn($restValue);

        $tagProvider->getTagForSectionId(5)->willReturn('section-5');
        $tagHandler->addTags(['section-5'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * ContentType.
     */
    public function it_does_nothing_on_content_type_draft(
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentType $restValue,
        TagHandler $tagHandler
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
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentType $restValue,
        TagHandler $tagHandler,
        TagProviderInterface $tagProvider
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $restValue->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);
        $event->getControllerResult()->willReturn($restValue);

        $tagProvider->getTagForTypeId(4)->willReturn('type-4');
        $tagHandler->addTags(['type-4'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * RestContentType.
     */
    public function it_does_nothing_on_rest_content_type_draft(
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        RestContentType $restValue,
        ContentType $contentType,
        TagHandler $tagHandler
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
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        RestContentType $restValue,
        ContentType $contentType,
        TagHandler $tagHandler,
        TagProviderInterface $tagProvider
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentType->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);
        $restValue->contentType = $contentType;
        $event->getControllerResult()->willReturn($restValue);

        $tagProvider->getTagForTypeId(4)->willReturn('type-4');
        $tagHandler->addTags(['type-4'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * ContentTypeGroupRefList.
     */
    public function it_does_nothing_on_rest_content_type_group_ref_draft(
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupRefList $restValue,
        ContentType $contentType,
        ContentTypeGroup $contentTypeGroup,
        TagHandler $tagHandler
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
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupRefList $restValue,
        ContentType $contentType,
        ContentTypeGroup $contentTypeGroup,
        TagHandler $tagHandler,
        TagProviderInterface $tagProvider
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentType->beConstructedWith([['id' => 4, 'status' => ContentType::STATUS_DEFINED]]);
        $restValue->contentType = $contentType;

        $contentTypeGroup->beConstructedWith([['id' => 2]]);
        $restValue->contentTypeGroups = [$contentTypeGroup];

        $event->getControllerResult()->willReturn($restValue);

        $tagProvider->getTagForTypeId(4)->willReturn('type-4');
        $tagProvider->getTagForTypeGroupId(2)->willReturn('type-group-2');
        $tagHandler->addTags(['type-4', 'type-group-2'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * ContentTypeGroupList.
     */
    public function it_writes_tags_on_rest_content_type_group_list(
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        ContentTypeGroupList $restValue,
        ContentTypeGroup $contentTypeGroup,
        TagHandler $tagHandler,
        TagProviderInterface $tagProvider
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentTypeGroup->beConstructedWith([['id' => 2]]);
        $restValue->contentTypeGroups = [$contentTypeGroup];
        $event->getControllerResult()->willReturn($restValue);

        $tagProvider->getTagForTypeGroupId(2)->willReturn('type-group-2');
        $tagHandler->addTags(['type-group-2'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }

    /**
     * VersionList.
     */
    public function it_writes_tags_on_rest_version_list(
        GetResponseForControllerResultEvent $event,
        Request $request,
        ParameterBag $attributes,
        VersionList $restValue,
        VersionInfo $versionInfo,
        ContentInfo $contentInfo,
        TagHandler $tagHandler,
        TagProviderInterface $tagProvider
    ) {
        $request->isMethodCacheable()->willReturn(true);
        $attributes->get('is_rest_request')->willReturn(true);

        $contentInfo->beConstructedWith([['id' => 33]]);
        $versionInfo->beConstructedWith([['contentInfo' => $contentInfo]]);
        $restValue->versions = [$versionInfo];
        $event->getControllerResult()->willReturn($restValue);

        $tagProvider->getTagForContentId(33)->willReturn('content-33');
        $tagProvider->getTagForContentVersions(33)->willReturn('content-versions-33');
        $tagHandler->addTags(['content-33', 'content-versions-33'])->shouldBecalled();
        $event->setControllerResult(new TypeToken(CachedValue::class))->shouldBecalled();

        $this->tagUIRestResult($event);
    }
}
