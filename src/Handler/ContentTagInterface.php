<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

/**
 * @since v0.9.3
 */
interface ContentTagInterface
{
    const CONTENT_PREFIX = 'content-';
    const LOCATION_PREFIX = 'location-';
    const PARENT_LOCATION_PREFIX = 'parent-';
    const PATH_PREFIX = 'path-';
    const RELATION_PREFIX = 'relation-';
    const RELATION_LOCATION_PREFIX = 'relation-location-';
    const CONTENT_TYPE_PREFIX = 'content-type-';

    /**
     * Low level tag method to add content tag.
     *
     * @see "docs/using_tags.md"
     * @param array $contentIds
     */
    public function addContentTags(array $contentIds);

    /**
     * Low level tag method to add location tag.
     *
     * @see "docs/using_tags.md"
     * @param array $locationIds
     */
    public function addLocationTags(array $locationIds);

    /**
     * Low level tag method to add parent location tag.
     *
     * @see "docs/using_tags.md"
     * @param array $parentLocationIds
     */
    public function addParentLocationTags(array $parentLocationIds);

    /**
     * Low level tag method to add location path tag.
     *
     * @see "docs/using_tags.md"
     * @param array $locationIds
     */
    public function addPathTags(array $locationIds);

    /**
     * Low level tag method to add relation tag.
     *
     * @see "docs/using_tags.md"
     * @param array $contentIds
     */
    public function addRelationTags(array $contentIds);

    /**
     * Low level tag method to add relation location tag.
     *
     * @see "docs/using_tags.md"
     * @param array $locationIds
     */
    public function addRelationLocationTags(array $locationIds);

    /**
     * Low level tag method to add relation location tag.
     *
     * @see "docs/using_tags.md"
     * @param array $contentTypeIds
     */
    public function addContentTypeTags(array $contentTypeIds);
}
