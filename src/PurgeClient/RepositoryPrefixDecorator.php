<?php

/**
 * File containing the RepositoryPrefixDecorator class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\RepositoryIdAwareTrait;

/**
 * RepositoryPrefixDecorator decorates the real purge client in order to prefix tags with respository id.
 *
 * Allows for multi repository usage against same proxy.
 */
class RepositoryPrefixDecorator implements PurgeClientInterface
{
    use RepositoryIdAwareTrait;

    /** @var \EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface */
    private $purgeClient;

    public function __construct(PurgeClientInterface $purgeClient)
    {
        $this->purgeClient = $purgeClient;
    }

    public function purge($tags)
    {
        if (empty($tags)) {
            return;
        }

        $tags = array_map(
            function ($tag) {
                // Obsolete: for BC with older purge calls for BAN based HttpCache impl
                $tag = is_numeric($tag) ? 'location-' . $tag : $tag;

                // Prefix tags with repository prefix
                return $this->repoPrefix . $tag;
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
