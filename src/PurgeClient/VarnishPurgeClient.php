<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use FOS\HttpCacheBundle\CacheManager;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

/**
 * Purge client based on FOSHttpCacheBundle.
 */
class VarnishPurgeClient implements PurgeClientInterface
{
    const INVALIDATE_TOKEN_PARAM = 'http_cache.varnish_invalidate_token';
    const INVALIDATE_TOKEN_PARAM_NAME = 'x-invalidate-token';
    const DEFAULT_HEADER_LENGTH = 7500;
    const XKEY_TAG_SEPERATOR = ' ';

    /**
     * @var \FOS\HttpCacheBundle\CacheManager
     */
    private $cacheManager;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    public function __construct(
        CacheManager $cacheManager,
        ConfigResolverInterface $configResolver
    ) {
        $this->cacheManager = $cacheManager;
        $this->configResolver = $configResolver;
    }

    public function purge($tags)
    {
        $this->cacheManager->invalidateTags($tags);
    }

    public function purgeAll()
    {
        $this->cacheManager->invalidateTags(['ez-all']);
    }
}
