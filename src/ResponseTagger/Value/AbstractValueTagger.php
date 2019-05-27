<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use EzSystems\PlatformHttpCacheBundle\Handler\TagHandler;
use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;

abstract class AbstractValueTagger implements ResponseTagger
{
    /** @var \EzSystems\PlatformHttpCacheBundle\Handler\TagHandler */
    protected $tagHandler;

    public function __construct(TagHandler $tagHandler)
    {
        $this->tagHandler = $tagHandler;
    }
}
