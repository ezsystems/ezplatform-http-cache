<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Delegator;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;

/**
 * Dispatches a value to all registered ResponseTaggers.
 */
class DispatcherTagger implements ResponseTagger
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger
     */
    private $taggers = [];

    public function __construct(array $taggers = [])
    {
        $this->taggers = $taggers;
    }

    public function tag($value)
    {
        foreach ($this->taggers as $tagger) {
            $tagger->tag($value);
        }
    }
}
