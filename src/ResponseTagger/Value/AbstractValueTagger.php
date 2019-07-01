<?php

namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger\Value;

use EzSystems\PlatformHttpCacheBundle\ResponseTagger\ResponseTagger;
use FOS\HttpCache\ResponseTagger as FosResponseTagger;

abstract class AbstractValueTagger implements ResponseTagger
{
    /** @var \FOS\HttpCache\ResponseTagger */
    protected $responseTagger;

    public function __construct(FosResponseTagger $responseTagger)
    {
        $this->responseTagger = $responseTagger;
    }
}
