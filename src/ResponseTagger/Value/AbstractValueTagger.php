<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use EzSystems\PlatformHttpCacheBundle\TagProvider\TagProviderInterface;
use FOS\HttpCache\Handler\TagHandler;

abstract class AbstractValueTagger implements ResponseTagger
{
    /** @var TagHandler */
    protected $tagHandler;

    /** @var TagProviderInterface */
    protected $tagProvider;

    public function __construct(TagHandler $tagHandler, TagProviderInterface $tagProvider)
    {
        $this->tagHandler = $tagHandler;
        $this->tagProvider = $tagProvider;
    }
}
