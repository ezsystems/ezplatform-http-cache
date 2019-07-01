<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\ProxyClient;

use FOS\HttpCache\ProxyClient\Varnish as FosVarnish;

class Varnish extends FosVarnish
{
    public function invalidateTags(array $tags)
    {
        $banMode = self::TAG_BAN === $this->options['tag_mode'];

        if ($banMode) {
            return parent::invalidateTags($tags);
        }

        $chunkSize = $this->determineTagsPerHeader($tags, ' ');

        foreach (array_chunk($tags, $chunkSize) as $tagchunk) {
            $this->invalidateTagsByPurge($tagchunk);
        }

        return $this;
    }

    protected function invalidateTagsByPurge(array $tagchunk)
    {
        $this->queueRequest(
            FosVarnish::HTTP_METHOD_PURGE,
            '/',
            [$this->options['tags_header'] => implode(' ', $tagchunk)],
            false
        );
    }
}
