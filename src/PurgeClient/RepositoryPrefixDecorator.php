<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;

/**
 * RepositoryPrefixDecorator decorates the real purge client in order to prefix tags with repository id.
 *
 * Also optionally allows for tags to be hashed to make sure tags are shortened (8 charters).
 *
 * Allows for multi repository usage against same proxy.
 */
class RepositoryPrefixDecorator implements PurgeClientInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface
     */
    private $purgeClient;

    /**
     * @var \EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix
     */
    private $prefixService;

    /**
     * @var bool
     */
    private $hashTags;

    public function __construct(PurgeClientInterface $purgeClient, RepositoryTagPrefix $prefixService, $hashTags = false)
    {
        $this->purgeClient = $purgeClient;
        $this->prefixService = $prefixService;
        $this->hashTags = $hashTags;
    }

    public function purge($tags)
    {
        if (empty($tags)) {
            return;
        }

        $repoPrefix = $this->prefixService->getRepositoryPrefix();
        $hashTags = $this->hashTags;
        $tags = array_map(
            static function ($tag) use ($repoPrefix, $hashTags) {
                // Obsolete: for BC with older purge calls for BAN based HttpCache impl
                $tag = is_numeric($tag) ? 'location-' . $tag : $tag;

                // Prefix tags with repository prefix
                if ($hashTags) {
                    return hash('crc32b', $repoPrefix . $tag);
                }

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
