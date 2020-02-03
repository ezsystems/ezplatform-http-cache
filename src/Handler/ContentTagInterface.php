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
}
