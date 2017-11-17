<?php

/**
 * File containing the Cache PurgeClientInterface class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use eZ\Publish\Core\MVC\Symfony\Cache\PurgeClientInterface as KernelPurgeClientInterface;

/**
 * KernelPurgeClientInterface is deprecated, and will be removed in a later version when compatibility with ezpublish-kernel 6.x is dropped.
 */
interface PurgeClientInterface extends KernelPurgeClientInterface
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
