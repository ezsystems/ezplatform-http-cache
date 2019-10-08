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
 * we have configured, and to be able to prefix tags with respository id in order to support multi repo setups.
 */
class TagHandler extends FOSTagHandler
{
    private $purgeClient;
    private $prefixService;
    private $tagsHeader;
    /** @var int|null */
    private $tagsHeaderMaxLength;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        CacheManager $cacheManager,
        $tagsHeader,
        PurgeClientInterface $purgeClient,
        RepositoryTagPrefix $prefixService,
        LoggerInterface $logger,
        $maxTagsHeaderLength = null
    ) {
        $this->tagsHeader = $tagsHeader;
        $this->purgeClient = $purgeClient;
        $this->prefixService = $prefixService;
        $this->logger = $logger;
        $this->tagsHeaderMaxLength = $maxTagsHeaderLength;

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
            if ($tags) {
                $tags = array_unique(array_merge($tags, explode(',', $this->getTagsHeaderValue())));
            } else {
                $tags = explode(',', $this->getTagsHeaderValue());
            }

            // Prefix tags with repository prefix (to be able to support several repositories on one proxy)
            $repoPrefix = $this->prefixService->getRepositoryPrefix();
            if (!empty($repoPrefix)) {
                $tags = array_map(
                    static function ($tag) use ($repoPrefix) {
                        return $repoPrefix . $tag;
                    },
                    $tags
                );
                // Also add a un-prefixed `ez-all` in order to be able to purge all across repos
                $tags[] = 'ez-all';
            }

            $tagsString = implode(' ', $tags);
            if ($this->tagsHeaderMaxLength && strlen($tagsString) > $this->tagsHeaderMaxLength) {
                $tagsString = trim(substr($tagsString, 0, strrpos(
                    substr($tagsString, 0, $this->tagsHeaderMaxLength + 1), ' '
                )));
                $this->logger->warning(
                    'HTTP Cache tags header max length reached and truncated to ' . $this->tagsHeaderMaxLength
                );
            }

            $response->headers->set($this->tagsHeader, $tagsString);
        }

        return $this;
    }
}
