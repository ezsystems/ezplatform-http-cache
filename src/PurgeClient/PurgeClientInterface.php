<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

/**
 * Interface for Purge Clients.
 */
interface PurgeClientInterface
{
    /**
     * Triggers the cache purge of $tags.
     *
     * It's up to the implementor to decide whether to purge tags right away or to delegate to a separate process.
     *
     * @param array $tags Array of tags to purge
     */
    public function purge($tags);

    /**
     * Purge the whole http cache.
     */
    public function purgeAll();
}
