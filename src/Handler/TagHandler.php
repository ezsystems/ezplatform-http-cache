<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use FOS\HttpCacheBundle\Handler\TagHandler as FOSTagHandler;
use Symfony\Component\HttpFoundation\Response;
use FOS\HttpCacheBundle\CacheManager;

/**
 * This is not a full implementation of FOS TagHandler
 * It extends extends TagHandler and implements invalidateTags() and purge() so that you may run
 * php app/console fos:httpcache:invalidate:tag <tag>.
 *
 * It implements tagResponse() to make sure TagSubscriber (a FOS event listener) sends tags using the header
 * we have configured, and to be able to prefix tags with respository id in order to support multi repo setups.
 */
class TagHandler extends FOSTagHandler
{
    private $cacheManager;
    private $purgeClient;
    private $prefixService;
    private $tagsHeader;
    private $hashTags;

    public function __construct(
        CacheManager $cacheManager,
        $tagsHeader,
        PurgeClientInterface $purgeClient,
        RepositoryTagPrefix $prefixService,
        $hashTags = false
    ) {
        $this->cacheManager = $cacheManager;
        $this->tagsHeader = $tagsHeader;
        $this->purgeClient = $purgeClient;
        $this->prefixService = $prefixService;
        $this->hashTags = $hashTags;

        parent::__construct($cacheManager, $tagsHeader);
        $this->addTags(['ez-all']);
    }

    /**
     * @deprecated Just an BC alias for invalidateTags().
     */
    public function purge($tags)
    {
        $this->invalidateTags($tags);
    }

    public function invalidateTags(array $tags)
    {
        // We don't hash tags here as it's done in PurgeClient\RepositoryPrefixDecorator
        $this->purgeClient->purge($tags);
    }

    public function tagResponse(Response $response, $replace = false)
    {
        $tags = [];
        if (!$replace && $response->headers->has($this->tagsHeader)) {
            $headers = $response->headers->get($this->tagsHeader, null, false);
            if (!empty($headers)) {
                // handle both both comma (FOS) and space (this bundle/xkey/fastly) separated strings
                // As there can be more requests going on, we don't add these to tag handler (ez-user-context-hash)
                $tags = preg_split("/[\s,]+/", implode(' ', $headers));
            }
        }

        // Should always be true due to "$this->addTags(['ez-all'])" in __construct().
        if ($this->hasTags()) {
            $tags = array_merge($tags, explode(',', $this->getTagsHeaderValue()));

            // Adapt tags like PurgeClient\RepositoryPrefixDecorator does:
            // - Prefix tags with repository prefix (to be able to support several repositories on one proxy)
            // - hash tags if enabled (in prod), in order to keep them short (8 characters)
            $repoPrefix = $this->prefixService->getRepositoryPrefix();
            $hashTags = $this->hashTags;
            $tags = array_map(
                static function ($tag) use ($repoPrefix, $hashTags) {
                    if ($hashTags) {
                        return hash('crc32b', $repoPrefix . $tag);
                    }

                    return $repoPrefix . $tag;
                },
                $tags
            );

            // Also add a un-prefixed `ez-all` and un-hashed ez-all
            $tags[] = 'ez-all';

            $response->headers->set($this->tagsHeader, implode(' ', array_unique($tags)));
        }

        return $this;
    }
}
