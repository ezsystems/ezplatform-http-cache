<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
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
class TagHandler extends FOSTagHandler implements TagHandlerInterface
{
    private $cacheManager;
    private $purgeClient;
    private $tagsHeader;
    private $repoPrefix;

    public function __construct(
        CacheManager $cacheManager,
        $tagsHeader,
        PurgeClientInterface $purgeClient,
        $repositoryId
    ) {
        $this->cacheManager = $cacheManager;
        $this->tagsHeader = $tagsHeader;
        $this->purgeClient = $purgeClient;
        $this->repoPrefix = empty($repositoryId) ? '' : $repositoryId . '_';
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
        if ($this->hasTags()) {
            $this->addTagHeaders($response, explode(',', $this->getTagsHeaderValue()));
        }

        return $this;
    }

    public function addTagHeaders(Response $response, array $tags)
    {
        if ($response->headers->has($this->tagsHeader)) {
            // Get as array and handle both array based and string based values
            $headerValue = $response->headers->get($this->tagsHeader, null, false);
            $tags = array_merge(
                $tags,
                count($headerValue) === 1 ? explode(' ', $headerValue[0]) : $headerValue
            );
        }

        // Prefix tags with repository prefix (to be able to support several repositories on one proxy)
        // But only if repo prefix is set (when not "default", see ctor), & if not already applied to tags
        if ($this->repoPrefix && strpos($tags[0], $this->repoPrefix) !== 0) {
            $tags = array_map(
                function ($tag) {
                    return $this->repoPrefix . $tag;
                },
                $tags
            );
        }

        $response->headers->set($this->tagsHeader, implode(' ', array_unique($tags)));
    }
}
