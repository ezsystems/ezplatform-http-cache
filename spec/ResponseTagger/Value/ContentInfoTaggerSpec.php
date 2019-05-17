<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\ContentInfoTagger;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;
use FOS\HttpCache\Handler\TagHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ContentInfoTaggerSpec extends ObjectBehavior
{
    public function let(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $this->beConstructedWith($tagHandler, $tagProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentInfoTagger::class);
    }

    public function it_ignores_non_content_info(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $this->tag(null);

        $tagProvider->getTagForContentId()->shouldNotHaveBeenCalled();
        $tagProvider->getTagForContentTypeId()->shouldNotHaveBeenCalled();
        $tagProvider->getTagForLocationId()->shouldNotHaveBeenCalled();

        $tagHandler->addTags()->shouldNotHaveBeenCalled();
    }

    public function it_tags_with_content_and_content_type_id(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $value = new ContentInfo(['id' => 123, 'contentTypeId' => 987]);

        $tagProvider->getTagForContentId(Argument::exact($value->id))->willReturn('content-123');
        $tagProvider->getTagForContentTypeId(Argument::exact($value->contentTypeId))->willReturn('content-type-987');

        $this->tag($value);

        $tagHandler->addTags(['content-123', 'content-type-987'])->shouldHaveBeenCalled();
    }

    public function it_tags_with_location_id_if_one_is_set(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $this->beConstructedWith($tagHandler, $tagProvider);

        $value = new ContentInfo(
            [
                'id' => 123,
                'mainLocationId' => 456,
                'contentTypeId' => 987,
            ]
        );

        $tagProvider->getTagForContentId(Argument::exact($value->id))->willReturn('content-123');
        $tagProvider->getTagForContentTypeId(Argument::exact($value->contentTypeId))->willReturn('content-type-987');
        $tagProvider->getTagForLocationId(Argument::exact($value->mainLocationId))->willReturn('location-456');

        $this->tag($value);

        $tagHandler->addTags(['content-123', 'content-type-987'])->shouldHaveBeenCalled();
        $tagHandler->addTags(['location-456'])->shouldHaveBeenCalled();
    }
}
