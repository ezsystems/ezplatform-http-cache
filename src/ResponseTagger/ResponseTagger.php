<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ResponseTagger;

use Symfony\Component\HttpFoundation\Response;

/**
 * Tags a Response based on data from a value.
 */
interface ResponseTagger
{
    /**
     * Extracts tags from a value.
     *
     * @param mixed $value
     */
    public function tag($value);
}
