<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
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
