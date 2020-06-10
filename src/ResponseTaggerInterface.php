<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle;

/**
 * Interface representing a future proof, autowirable, response tagger.
 */
interface ResponseTaggerInterface
{
    public function addTags(array $tags);
}
