<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\TagProvider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProvider;
use PhpSpec\ObjectBehavior;

class TagProviderSpec extends ObjectBehavior
{
    const TAGS_MAP = [
        'short' => [
            'location' => 'l',
            'content' => 'c',
            'contentType' => 'ct',
            'contentVersions' => 'cv',
            'parent' => 'p',
            'relation' => 'r',
            'relationLocation' => 'rl',
            'path' => 'pa',
            'type' => 't',
            'typeGroup' => 'tg',
            'section' => 's',
            'all' => 'ea'
        ],
        'long' => [
            'location' => 'location',
            'content' => 'content',
            'contentType' => 'content-type',
            'contentVersions' => 'content-versions',
            'parent' => 'parent',
            'relation' => 'relation',
            'relationLocation' => 'relation-location',
            'path' => 'path',
            'type' => 'type',
            'typeGroup' => 'type-group',
            'section' => 'section',
            'all' => 'ez-all',
        ]
    ];

    public function let(ConfigResolverInterface $configResolver)
    {
        $this->beConstructedWith($configResolver, self::TAGS_MAP, 'ez-user-context-hash');
        $configResolver->getParameter('http_cache.tag_format')->willReturn('short');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(TagProvider::class);
    }

    public function it_provides_tag_for_location_id()
    {
        $locationId = 12;
        $this->getTagForLocationId($locationId)->shouldReturn('l-' . $locationId);
    }

    public function it_provides_tag_for_content_id()
    {
        $contentId = 12;
        $this->getTagForContentId($contentId)->shouldReturn('c-' . $contentId);
    }

    public function it_provides_tag_for_content_type_id()
    {
        $contentTypeId = 12;
        $this->getTagForContentTypeId($contentTypeId)->shouldReturn('ct-' . $contentTypeId);
    }

    public function it_provides_tag_for_content_versions()
    {
        $versions = 12;
        $this->getTagForContentVersions($versions)->shouldReturn('cv-' . $versions);
    }

    public function it_provides_tag_for_parent_id()
    {
        $parentId = 12;
        $this->getTagForParentId($parentId)->shouldReturn('p-' . $parentId);
    }

    public function it_provides_tag_for_relation_id()
    {
        $relationId = 12;
        $this->getTagForRelationId($relationId)->shouldReturn('r-' . $relationId);
    }

    public function it_provides_tag_for_relation_location_id()
    {
        $locationId = 12;
        $this->getTagForRelationLocationId($locationId)->shouldReturn('rl-' . $locationId);
    }

    public function it_provides_tag_for_path_id()
    {
        $locationId = 12;
        $this->getTagForPathId($locationId)->shouldReturn('pa-' . $locationId);
    }

    public function it_provides_tag_for_section_id()
    {
        $sectionId = 12;
        $this->getTagForSectionId($sectionId)->shouldReturn('s-' . $sectionId);
    }

    public function it_provides_tag_for_type_id()
    {
        $typeId = 12;
        $this->getTagForTypeId($typeId)->shouldReturn('t-' . $typeId);
    }

    public function it_provides_tag_for_type_group_id()
    {
        $typeGroupId = 12;
        $this->getTagForTypeGroupId($typeGroupId)->shouldReturn('tg-' . $typeGroupId);
    }

    public function it_provides_tag_for_all()
    {
        $this->getTagForAll()->shouldReturn('ea');
    }

    public function it_provides_tag_for_user_context_hash()
    {
        $this->getTagForUserContextHash()->shouldReturn('ez-user-context-hash');
    }
}
