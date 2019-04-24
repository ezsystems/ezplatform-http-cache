<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\PlatformHttpCacheBundle;

use FOS\HttpCacheBundle\Http\SymfonyResponseTagger as FOSSymfonyResponseTagger;
use Symfony\Component\HttpFoundation\Response;

class SymfonyResponseTagger extends FOSSymfonyResponseTagger
{
    /** @var string */
    private $tagsHeader;

    /** @var string */
    private $repoPrefix;

    public function __construct(string $tagsHeader, array $options = [], string $repoPrefix = '')
    {
        parent::__construct($options);

        $this->tagsHeader = $tagsHeader;
        $this->repoPrefix = $repoPrefix;
    }

    public function setRepositoryId(string $repositoryId): void
    {
        $this->repoPrefix = empty($repositoryId) ? '' : $repositoryId.'_';
    }

    public function tagSymfonyResponse(Response $response, $replace = false): self
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
            if (!empty($this->repoPrefix)) {
                $tags = array_map(
                    function ($tag) {
                        return $this->repoPrefix.$tag;
                    },
                    $tags
                );
                // Also add a un-prefixed `ez-all` in order to be able to purge all across repos
                $tags[] = 'ez-all';
            }

            $response->headers->set($this->tagsHeader, implode(' ', array_unique($tags)));
        }

        return $this;
    }
}
