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
class TagHandler extends FOSTagHandler
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
        if (!$replace && $response->headers->has($this->tagsHeader)) {
            $header = $response->headers->get($this->tagsHeader);
            if (!empty($header)) {
                // handle both both comma (FOS) and space (this bundle/xkey/fastly) seperated strings
                $this->addTags(preg_split("/[\s,]+/", $header));
            }
        }

        if ($this->hasTags()) {
            $tags = explode(',', $this->getTagsHeaderValue());

            // Prefix tags with repository prefix (to be able to support several repositories on one proxy)
            if ($this->repoPrefix) {
                $tags = array_map(
                    function ($tag) {
                        return $this->repoPrefix . $tag;
                    },
                    $tags
                );
            }

            $response->headers->set($this->tagsHeader, implode(' ', array_unique($tags)));
        }

        return $this;
    }
}
