<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use FOS\HttpCacheBundle\Handler\TagHandler as FOSTagHandler;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use FOS\HttpCacheBundle\CacheManager;

/**
 * This is not a full implementation of FOS TagHandler
 * It extends extends TagHandler and implements invalidateTags() and purge() so that you may run
 * php app/console fos:httpcache:invalidate:tag <tag>.
 *
 * It implements tagResponse() to make sure TagSubscriber (a FOS event listener) sends tags using the header
 * we have configured, and to be able to prefix tags with repository id in order to support multi repo setups.
 */
class TagHandler extends FOSTagHandler implements ContentTagInterface
{
    private $purgeClient;
    private $prefixService;
    private $tagsHeader;
    /** @var \Psr\Log\LoggerInterface */
    private $logger;
    /** @var int|null */
    private $tagsHeaderMaxLength;
    /** @var int|null */
    private $tagsHeaderReducedTTl;

    public function __construct(
        CacheManager $cacheManager,
        $tagsHeader,
        PurgeClientInterface $purgeClient,
        RepositoryTagPrefix $prefixService,
        LoggerInterface $logger,
        $maxTagsHeaderLength = null,
        $tagsHeaderReducedTTl = null
    ) {
        $this->tagsHeader = $tagsHeader;
        $this->purgeClient = $purgeClient;
        $this->prefixService = $prefixService;
        $this->logger = $logger;
        $this->tagsHeaderMaxLength = $maxTagsHeaderLength;
        $this->tagsHeaderReducedTTl = $tagsHeaderReducedTTl;

        parent::__construct($cacheManager, $tagsHeader);
        $this->addTags(['ez-all']);
    }

    public function invalidateTags(array $tags)
    {
        $this->purge($tags);
    }

    public function purge($tags)
    {
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

        if ($this->hasTags()) {
            $tags = array_merge($tags, explode(',', $this->getTagsHeaderValue()));

            // Prefix tags with repository prefix (to be able to support several repositories on one proxy)
            $repoPrefix = $this->prefixService->getRepositoryPrefix();
            $tags = array_map(
                static function ($tag) use ($repoPrefix) {
                    // Deprecated, map content tags from old to new format to make sure we get only short + unique tags
                    $tag = str_replace(
                        ['content-', 'location-', 'path-', 'parent-', 'relation-', 'relation-location-', 'content-type-'],
                        [
                            ContentTagInterface::CONTENT_PREFIX,
                            ContentTagInterface::LOCATION_PREFIX,
                            ContentTagInterface::PATH_PREFIX,
                            ContentTagInterface::PARENT_LOCATION_PREFIX,
                            ContentTagInterface::RELATION_PREFIX,
                            ContentTagInterface::RELATION_LOCATION_PREFIX,
                            ContentTagInterface::CONTENT_TYPE_PREFIX,
                        ],
                        $tag
                    );

                    return $repoPrefix . $tag;
                },
                $tags
            );

            if ($repoPrefix !== '') {
                // An un-prefixed `ez-all` for purging across repos, add to start of array to avoid being truncated
                array_unshift($tags, 'ez-all');
            }

            $tagsString = implode(' ', array_unique($tags));
            $tagsLength = strlen($tagsString);
            if ($this->tagsHeaderMaxLength && $tagsLength > $this->tagsHeaderMaxLength) {
                $tagsString = substr(
                    $tagsString,
                    0,
                    // Seek backwards from point of max length using negative offset
                    strrpos($tagsString, ' ', $this->tagsHeaderMaxLength - $tagsLength)
                );

                $responseSharedMaxAge = $response->headers->getCacheControlDirective('s-maxage');
                if (
                    $this->tagsHeaderReducedTTl &&
                    $responseSharedMaxAge &&
                    $this->tagsHeaderReducedTTl < $responseSharedMaxAge
                ) {
                    $response->setSharedMaxAge($this->tagsHeaderReducedTTl);
                }

                $this->logger->warning(
                    'HTTP Cache tags header max length reached and truncated to ' . $this->tagsHeaderMaxLength
                );
            }

            $response->headers->set($this->tagsHeader, $tagsString);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addContentTags(array $contentIds)
    {
        $this->addTags(array_map(static function ($contentId) {
            return ContentTagInterface::CONTENT_PREFIX . $contentId;
        }, $contentIds));
    }

    /**
     * {@inheritdoc}
     */
    public function addLocationTags(array $locationIds)
    {
        $this->addTags(array_map(static function ($locationId) {
            return ContentTagInterface::LOCATION_PREFIX . $locationId;
        }, $locationIds));
    }

    /**
     * {@inheritdoc}
     */
    public function addParentLocationTags(array $parentLocationIds)
    {
        $this->addTags(array_map(static function ($parentLocationId) {
            return ContentTagInterface::PARENT_LOCATION_PREFIX . $parentLocationId;
        }, $parentLocationIds));
    }

    /**
     * {@inheritdoc}
     */
    public function addPathTags(array $locationIds)
    {
        $this->addTags(array_map(static function ($locationId) {
            return ContentTagInterface::PATH_PREFIX . $locationId;
        }, $locationIds));
    }

    /**
     * {@inheritdoc}
     */
    public function addRelationTags(array $contentIds)
    {
        $this->addTags(array_map(static function ($contentId) {
            return ContentTagInterface::RELATION_PREFIX . $contentId;
        }, $contentIds));
    }

    /**
     * {@inheritdoc}
     */
    public function addRelationLocationTags(array $locationIds)
    {
        $this->addTags(array_map(static function ($locationId) {
            return ContentTagInterface::RELATION_LOCATION_PREFIX . $locationId;
        }, $locationIds));
    }

    /**
     * {@inheritdoc}
     */
    public function addContentTypeTags(array $contentTypeIds)
    {
        $this->addTags(array_map(static function ($contentTypeId) {
            return ContentTagInterface::CONTENT_TYPE_PREFIX . $contentTypeId;
        }, $contentTypeIds));
    }
}
