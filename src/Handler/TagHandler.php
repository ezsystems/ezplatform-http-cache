<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is not a full implementation of FOS TagHandler
 * It extends extends TagHandler and implements invalidateTags() and purge() so that you may run
 * php app/console fos:httpcache:invalidate:tag <tag>.
 *
 * It implements tagResponse() to make sure TagSubscriber (a FOS event listener) sends tags using the header
 * we have configured, and to be able to prefix tags with repository id in order to support multi repo setups.
 */
class TagHandler extends SymfonyResponseTagger implements ContentTagInterface
{
    /** @var \EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix */
    private $prefixService;

    /** @var \Psr\Log\LoggerInterface */
    private $logger;

    /** @var int|null */
    private $tagsHeaderMaxLength;

    /** @var int|null */
    private $tagsHeaderReducedTTl;

    public function __construct(
        RepositoryTagPrefix $prefixService,
        LoggerInterface $logger,
        array $options = []
    ) {
        $this->prefixService = $prefixService;
        $this->logger = $logger;

        if (array_key_exists('tag_max_length', $options)) {
            $this->tagsHeaderMaxLength = $options['tag_max_length'];
            unset($options['tag_max_length']);
        }

        if (array_key_exists('tag_max_length_ttl', $options)) {
            $this->tagsHeaderReducedTTl = $options['tag_max_length_ttl'];
            unset($options['tag_max_length_ttl']);
        }

        parent::__construct($options);
        $this->addTags(['ez-all']);
    }

    public function tagSymfonyResponse(Response $response, $replace = false)
    {
        $tags = [];
        if (!$replace && $response->headers->has($this->getTagsHeaderName())) {
            $headers = $response->headers->all($this->getTagsHeaderName());
            if (!empty($headers)) {
                // handle both both comma (FOS) and space (this bundle/xkey/fastly) separated strings
                // As there can be more requests going on, we don't add these to tag handler (ez-user-context-hash)
                $tags = preg_split("/[\s,]+/", implode(' ', $headers));
            }
        }

        if ($this->hasTags()) {
            $tags = array_merge($tags, preg_split("/[\s,]+/", $this->getTagsHeaderValue()));

            // Prefix tags with repository prefix (to be able to support several repositories on one proxy)
            $repoPrefix = $this->prefixService->getRepositoryPrefix();
            if ($repoPrefix !== '') {
                $tags = array_map(
                    static function ($tag) use ($repoPrefix) {
                        return $repoPrefix . $tag;
                    },
                    $tags
                );

                // An un-prefixed `ez-all` for purging across repos, add to start of array to avoid being truncated
                array_unshift($tags, 'ez-all');
            }

            //Clear unprefixed tags
            $this->clear();

            $this->addTags($tags);
            $tagsString = $this->getTagsHeaderValue();
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

            $response->headers->set($this->getTagsHeaderName(), $tagsString);
            $this->clear();
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
