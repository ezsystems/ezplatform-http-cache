<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use FOS\HttpCacheBundle\CacheManager;

/**
 * Purge client based on FOSHttpCacheBundle.
 */
class VarnishPurgeClient implements PurgeClientInterface
{
    /** @var \FOS\HttpCacheBundle\CacheManager */
    private $cacheManager;

    public function __construct(
        CacheManager $cacheManager
    ) {
        $this->cacheManager = $cacheManager;
    }

    public function purge(array $tags): void
    {
        $this->cacheManager->invalidateTags($tags);
    }

    public function purgeAll(): void
    {
        $this->cacheManager->invalidateTags(['ez-all']);
    }
}
