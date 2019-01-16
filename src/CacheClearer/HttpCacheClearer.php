<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\CacheClearer;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

/**
 * Cache cleared for purging all http cache on cache:clear.
 */
class HttpCacheClearer implements CacheClearerInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface
     */
    protected $purgeClient;

    public function __construct(PurgeClientInterface $purgeClient)
    {
        $this->purgeClient = $purgeClient;
    }

    public function clear($cacheDirectory)
    {
        // In the case of Varnish & Fastly this results in a expiry and not a purge
        // Meaning the cache items might still be served up to the time of the grace/stale period

        // @todo: Ideally should be a way to opt out of this when needed, but cache warmers is not a good match either
        $this->purgeClient->purgeAll();
    }
}
