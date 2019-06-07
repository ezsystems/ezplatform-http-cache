<?php

/**
 * File containing the TagAwareStore class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Proxy;

use EzSystems\PlatformHttpCacheBundle\RequestAwarePurger;
use Symfony\Component\HttpFoundation\Request;
use Toflar\Psr6HttpCacheStore\Psr6Store;

/**
 * TagAwareStore implements all the logic for storing cache metadata regarding tags (locations, content type, ..).
 */
class TagAwareStore extends Psr6Store implements RequestAwarePurger
{
    /**
     * Purges data from $request.
     * If key or X-Location-Id (deprecated) header is present, the store will purge cache for given locationId or group of locationIds.
     * If not, regular purge by URI will occur.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return bool True if purge was successful. False otherwise
     */
    public function purgeByRequest(Request $request)
    {
        return parent::purge($request->getUri());
    }
}
