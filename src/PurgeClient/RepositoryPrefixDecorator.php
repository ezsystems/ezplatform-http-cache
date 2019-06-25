<?php

/**
 * File containing the RepositoryPrefixDecorator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;

/**
 * RepositoryPrefixDecorator decorates the real purge client in order to prefix tags with respository id.
 *
 * Allows for multi repository usage against same proxy.
 */
class RepositoryPrefixDecorator implements PurgeClientInterface
{
    /** @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface */
    private $purgeClient;

    /** @var \EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix */
    private $prefixService;

    public function __construct(PurgeClientInterface $purgeClient, RepositoryTagPrefix $prefixService)
    {
        $this->purgeClient = $purgeClient;
        $this->prefixService = $prefixService;
    }

    public function purge($tags): void
    {
        if (empty($tags)) {
            return;
        }

        $repoPrefix = $this->prefixService->getRepositoryPrefix();
        $tags = array_map(
            static function ($tag) use ($repoPrefix) {
                // Prefix tags with repository prefix
                return $repoPrefix . $tag;
            },
            (array)$tags
        );

        $this->purgeClient->purge($tags);
    }

    public function purgeAll(): void
    {
        $this->purgeClient->purgeAll();
    }
}
