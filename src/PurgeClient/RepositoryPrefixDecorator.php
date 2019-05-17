<?php

/**
 * File containing the RepositoryPrefixDecorator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;

/**
 * RepositoryPrefixDecorator decorates the real purge client in order to prefix tags with respository id.
 *
 * Allows for multi repository usage against same proxy.
 */
class RepositoryPrefixDecorator implements PurgeClientInterface
{
    /** @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface */
    private $purgeClient;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix
     */
    private $prefixService;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface
     */
    private $tagProvider;

    public function __construct(PurgeClientInterface $purgeClient, RepositoryTagPrefix $prefixService, TagProviderInterface $tagProvider)
    {
        $this->purgeClient = $purgeClient;
        $this->prefixService = $prefixService;
        $this->tagProvider = $tagProvider;
    }

    public function purge($tags)
    {
        if (empty($tags)) {
            return;
        }

        $repoPrefix = $this->prefixService->getRepositoryPrefix();
        $tags = array_map(
            function ($tag) use ($repoPrefix) {
                // Obsolete: for BC with older purge calls for BAN based HttpCache impl
                $tag = is_numeric($tag) ? $this->tagProvider->getTagForLocationId($tag) : $tag;

                // Prefix tags with repository prefix
                return $repoPrefix . $tag;
            },
            (array)$tags
        );

        $this->purgeClient->purge($tags);
    }

    public function purgeAll()
    {
        $this->purgeClient->purgeAll();
    }
}
