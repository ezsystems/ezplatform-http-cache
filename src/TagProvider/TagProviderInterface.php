<?php

/**
 * File containing the TagProviderInterface.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\TagProvider;

interface TagProviderInterface
{
    const LOCATION_KEY = 'location';
    const CONTENT_KEY = 'content';
    const CONTENT_TYPE_KEY = 'contentType';
    const CONTENT_VERSIONS_KEY = 'contentVersions';
    const PARENT_KEY = 'parent';
    const RELATION_KEY = 'relation';
    const RELATION_LOCATION_KEY = 'relationLocation';
    const PATH_KEY = 'path';
    const TYPE_KEY = 'type';
    const TYPE_GROUP_KEY = 'typeGroup';
    const SECTION_KEY = 'section';
    const ALL_KEY = 'all';
    const DELIMITER = '-';

    /**
     * @param int $locationId
     * @return string
     */
    public function getTagForLocationId($locationId);

    /**
     * @param int $contentId
     * @return string
     */
    public function getTagForContentId($contentId);

    /**
     * @param int $contentTypeId
     * @return string
     */
    public function getTagForContentTypeId($contentTypeId);

    /**
     * @param int $contentInfoId
     * @return string
     */
    public function getTagForContentVersions($contentInfoId);

    /**
     * @param int $parentId
     * @return string
     */
    public function getTagForParentId($parentId);

    /**
     * @param int $contentId
     * @return string
     */
    public function getTagForRelationId($contentId);

    /**
     * @param int $reverseRelationId
     * @return string
     */
    public function getTagForRelationLocationId($reverseRelationId);

    /**
     * @param int $pathId
     * @return string
     */
    public function getTagForPathId($pathId);

    /**
     * @param int $sectionId
     * @return string
     */
    public function getTagForSectionId($sectionId);

    /**
     * @param int $typeId
     * @return string
     */
    public function getTagForTypeId($typeId);

    /**
     * @param int $typeGroupId
     * @return string
     */
    public function getTagForTypeGroupId($typeGroupId);

    /**
     * @return string
     */
    public function getTagForAll();

    /**
     * @return string
     */
    public function getTagForUserContextHash();
}
