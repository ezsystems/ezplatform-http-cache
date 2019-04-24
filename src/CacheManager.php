<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\PlatformHttpCacheBundle;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use FOS\HttpCache\ProxyClient\ProxyClient;
use FOS\HttpCacheBundle\CacheManager as FOSCacheManager;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CacheManager extends FOSCacheManager
{
    /** @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface */
    private $purgeClient;

    public function __construct(
        ProxyClient $cache,
        UrlGeneratorInterface $urlGenerator,
        PurgeClientInterface $purgeClient
    ) {
        parent::__construct($cache, $urlGenerator);
        $this->purgeClient = $purgeClient;
    }

    public function invalidateTags(array $tags): self
    {
        $this->purgeClient->purge($tags);

        return $this;
    }
}
