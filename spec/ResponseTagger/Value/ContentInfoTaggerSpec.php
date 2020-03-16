<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\ContentInfoTagger;
use FOS\HttpCache\ResponseTagger;
use PhpSpec\ObjectBehavior;

class ContentInfoTaggerSpec extends ObjectBehavior
{
    public function let(ResponseTagger $tagHandler)
    {
        $this->beConstructedWith($tagHandler);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(ContentInfoTagger::class);
    }

    public function it_ignores_non_content_info(ResponseTagger $tagHandler)
    {
        $this->tag(null);

        $tagHandler->addTags()->shouldNotHaveBeenCalled();
    }

    public function it_tags_with_content_and_content_type_id(ResponseTagger $tagHandler)
    {
        $value = new ContentInfo(['id' => 123, 'contentTypeId' => 987]);

        $this->tag($value);

        $tagHandler->addTags(['c123', 'ct987'])->shouldHaveBeenCalled();
    }

    public function it_tags_with_location_id_if_one_is_set(ResponseTagger $tagHandler)
    {
        $value = new ContentInfo(['mainLocationId' => 456]);

        $this->tag($value);

        $tagHandler->addTags(['l456'])->shouldHaveBeenCalled();
    }
}
