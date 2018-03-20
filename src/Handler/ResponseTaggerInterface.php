<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

/**
 * @since v0.4.3
 */
interface ResponseTaggerInterface
{
    /**
     * @param array $tags
     *
     * @return $this
     */
    public function addTags(array $tags);

    /**
     * @return bool True if this handler will set at least one tag
     */
    public function hasTags();
}
