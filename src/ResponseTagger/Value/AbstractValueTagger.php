<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use FOS\HttpCache\Handler\TagHandler;

abstract class AbstractValueTagger implements ResponseTagger
{
    /** @var TagHandler */
    protected $tagHandler;

    public function __construct(TagHandler $tagHandler)
    {
        $this->tagHandler = $tagHandler;
    }
}
