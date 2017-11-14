<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle;

use eZ\Bundle\EzPublishCoreBundle\HttpCache;
use EzSystems\PlatformHttpCacheBundle\Proxy\TagAwareStore;

/**
 * Custom AppCache.
 *
 * Enable by setting SYMFONY_HTTP_CACHE_CLASS to 'EzSystems\PlatformHttpCacheBundle\AppCache'
 */
class AppCache extends HttpCache
{
    protected function createStore()
    {
        return new TagAwareStore($this->cacheDir ?: $this->kernel->getCacheDir() . '/http_cache');
    }
}
