<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value\LocationTagger;
use eZ\Publish\Core\Repository\Values\Content\Location;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;
use FOS\HttpCache\Handler\TagHandler;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LocationTaggerSpec extends ObjectBehavior
{
    public function let(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $this->beConstructedWith($tagHandler, $tagProvider);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(LocationTagger::class);
    }

    public function it_ignores_non_location(TagHandler $tagHandler)
    {
        $this->tag(null);

        $tagHandler->addTags(Argument::any())->shouldNotHaveBeenCalled();
    }

    public function it_tags_with_location_id_if_not_main_location(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $value = new Location(['id' => 123, 'parentLocationId' => 2, 'contentInfo' => new ContentInfo(['mainLocationId' => 321])]);

        $tagProvider->getTagForLocationId(123)->willReturn('location-123');
        $tagProvider->getTagForParentId(2)->willReturn('parent-2');

        $this->tag($value);

        $tagHandler->addTags(['location-123'])->shouldHaveBeenCalled();
    }

    public function it_tags_with_parent_location_id(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $value = new Location(['id' => 2, 'parentLocationId' => 123, 'contentInfo' => new ContentInfo(['mainLocationId' => 2])]);

        $tagProvider->getTagForParentId(123)->willReturn('parent-123');

        $this->tag($value);

        $tagHandler->addTags(['parent-123'])->shouldHaveBeenCalled();
    }

    public function it_tags_with_path_items(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $value = new Location(['id' => 12, 'parentLocationId' => 2, 'pathString' => '/1/2/123', 'contentInfo' => new ContentInfo()]);

        $tagProvider->getTagForLocationId(12)->willReturn('location-12');
        $tagProvider->getTagForParentId(2)->willReturn('location-12');
        $tagProvider->getTagForPathId(1)->willReturn('path-1');
        $tagProvider->getTagForPathId(2)->willReturn('path-2');
        $tagProvider->getTagForPathId(123)->willReturn('path-123');

        $this->tag($value);

        $tagHandler->addTags(['location-12'])->shouldHaveBeenCalled();
        $tagHandler->addTags(['path-1', 'path-2', 'path-123'])->shouldHaveBeenCalled();
    }
}
