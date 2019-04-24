<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use FOS\HttpCacheBundle\Http\SymfonyResponseTagger;

abstract class AbstractValueTagger implements ResponseTagger
{
    /** @var \FOS\HttpCacheBundle\Http\SymfonyResponseTagger */
    protected $symfonyResponseTagger;

    public function __construct(SymfonyResponseTagger $symfonyResponseTagger)
    {
        $this->symfonyResponseTagger = $symfonyResponseTagger;
    }
}
