<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
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
