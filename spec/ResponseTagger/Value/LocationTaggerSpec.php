<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\LocationTagger;
use eZ\Publish\Core\Repository\Values\Content\Location;
use FOS\HttpCache\ResponseTagger;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LocationTaggerSpec extends ObjectBehavior
{
    public function let(ResponseTagger $tagHandler)
    {
        $this->beConstructedWith($tagHandler);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(LocationTagger::class);
    }

    public function it_ignores_non_location(ResponseTagger $tagHandler)
    {
        $this->tag(null);

        $tagHandler->addTags(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function it_tags_with_location_id_if_not_main_location(ResponseTagger $tagHandler)
    {
        $value = new Location(['id' => 123, 'contentInfo' => new ContentInfo(['mainLocationId' => 321])]);
        $this->tag($value);

        $tagHandler->addTags(['location-123'])->shouldHaveBeenCalled();
    }

    public function it_tags_with_parent_location_id(ResponseTagger $tagHandler)
    {
        $value = new Location(['parentLocationId' => 123, 'contentInfo' => new ContentInfo()]);

        $this->tag($value);

        $tagHandler->addTags(['parent-123'])->shouldHaveBeenCalled();
    }

    public function it_tags_with_path_items(ResponseTagger $tagHandler)
    {
        $value = new Location(['pathString' => '/1/2/123', 'contentInfo' => new ContentInfo()]);

        $this->tag($value);

        $tagHandler->addTags(['path-1', 'path-2', 'path-123'])->shouldHaveBeenCalled();
    }
}
