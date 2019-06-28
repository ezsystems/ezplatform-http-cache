<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Handler;

use EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;
use Symfony\Component\HttpFoundation\Response;

/**
 * This is not a full implementation of FOS TagHandler
 * It extends extends TagHandler and implements invalidateTags() and purge() so that you may run
 * php app/console fos:httpcache:invalidate:tag <tag>.
 *
 * It implements tagResponse() to make sure TagSubscriber (a FOS event listener) sends tags using the header
 * we have configured, and to be able to prefix tags with respository id in order to support multi repo setups.
 */
class TagHandler extends SymfonyResponseTagger
{
    /** @var \EzSystems\PlatformHttpCacheBundle\RepositoryTagPrefix */
    private $prefixService;

    /** @var string */
    private $tagsHeader;

    public function __construct(
        string $tagsHeader,
        RepositoryTagPrefix $prefixService,
        array $options = []
    ) {
        $this->tagsHeader = $tagsHeader;
        $this->prefixService = $prefixService;

        parent::__construct($options);
        $this->addTags(['ez-all']);
    }

    public function tagSymfonyResponse(Response $response, $replace = false)
    {
        if (!$this->hasTags()) {
            return $this;
        }

        $tags = [];
        if (!$replace && $response->headers->has($this->getTagsHeaderName())) {
            $header = $response->headers->get($this->getTagsHeaderName());
            if ('' !== $header) {
                $tags = explode(',', $response->headers->get($this->getTagsHeaderName()));
            }
        }

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
        $this->addTags($tags);

        $response->headers->set($this->getTagsHeaderName(), $this->getTagsHeaderValue());
        $this->clear();

        return $this;
    }
}
