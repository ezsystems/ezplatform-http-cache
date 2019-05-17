<?php

namespace EzSystems\PlatformHttpCacheBundle\TagProvider;

use eZ\Publish\Core\MVC\ConfigResolverInterface;

final class TagProvider implements TagProviderInterface
{
    /**
     * @var array
     */
    private $tagsMap;

    /**
     * @var string
     */
    private $userContextHashTag;

    /**
     * @var string
     */
    private $type;

    public function __construct(ConfigResolverInterface $configResolver, array $tagsMap, $userContextHashTag)
    {
        $this->userContextHashTag = $userContextHashTag;
        $this->tagsMap = $tagsMap;
        $this->type = $configResolver->getParameter('http_cache.tag_format');
    }

    public function getTagForLocationId($locationId)
    {
        return $this->tagsMap[$this->type][self::LOCATION_KEY] . self::DELIMITER . (string)$locationId;
    }

    public function getTagForContentId($contentId)
    {
        return $this->tagsMap[$this->type][self::CONTENT_KEY] . self::DELIMITER . (string)$contentId;
    }

    public function getTagForContentTypeId($contentTypeId)
    {
        return $this->tagsMap[$this->type][self::CONTENT_TYPE_KEY] . self::DELIMITER . (string)$contentTypeId;
    }

    public function getTagForContentVersions($contentInfoId)
    {
        return $this->tagsMap[$this->type][self::CONTENT_VERSIONS_KEY] . self::DELIMITER . (string)$contentInfoId;
    }

    public function getTagForParentId($parentId)
    {
        return $this->tagsMap[$this->type][self::PARENT_KEY] . self::DELIMITER . (string)$parentId;
    }

    public function getTagForRelationId($contentId)
    {
        return $this->tagsMap[$this->type][self::RELATION_KEY] . self::DELIMITER . (string)$contentId;
    }

    public function getTagForRelationLocationId($reverseRelationId)
    {
        return $this->tagsMap[$this->type][self::RELATION_LOCATION_KEY] . self::DELIMITER . (string)$reverseRelationId;
    }

    public function getTagForPathId($pathId)
    {
        return $this->tagsMap[$this->type][self::PATH_KEY] . self::DELIMITER . (string)$pathId;
    }

    public function getTagForSectionId($sectionId)
    {
        return $this->tagsMap[$this->type][self::SECTION_KEY] . self::DELIMITER . (string)$sectionId;
    }

    public function getTagForTypeId($typeId)
    {
        return $this->tagsMap[$this->type][self::TYPE_KEY] . self::DELIMITER . (string)$typeId;
    }

    public function getTagForTypeGroupId($typeGroupId)
    {
        return $this->tagsMap[$this->type][self::TYPE_GROUP_KEY] . self::DELIMITER . (string)$typeGroupId;
    }

    public function getTagForAll()
    {
        return $this->tagsMap[$this->type][self::ALL_KEY];
    }

    public function getTagForUserContextHash()
    {
        return $this->userContextHashTag;
    }
}
