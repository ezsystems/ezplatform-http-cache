<?php

/**
 * File containing the UserContextSubscriber class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\Proxy;

use FOS\HttpCache\SymfonyCache\UserContextListener as BaseUserContextSubscriber;
use Symfony\Component\HttpFoundation\Request;

/**
 * Extends UserContextSubscriber from FOSHttpCache to include original request.
 *
 * {@inheritdoc}
 */
class UserContextSubscriber extends BaseUserContextSubscriber
{
    protected function cleanupHashLookupRequest(Request $hashLookupRequest, Request $originalRequest)
    {
        parent::cleanupHashLookupRequest($hashLookupRequest, $originalRequest);
        // Embed the original request as we need it to match the SiteAccess.
        $hashLookupRequest->attributes->set('_ez_original_request', $originalRequest);
    }
}
