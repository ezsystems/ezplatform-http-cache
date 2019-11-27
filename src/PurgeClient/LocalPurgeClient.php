<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use EzSystems\PlatformHttpCacheBundle\RequestAwarePurger;
use Symfony\Component\HttpFoundation\Request;

/**
 * LocalPurgeClient emulates an Http PURGE request to be received by the Proxy Tag cache store.
 * Handy for single-serve using Symfony Proxy..
 */
class LocalPurgeClient implements PurgeClientInterface
{
    /**
     * @var \EzSystems\PlatformHttpCacheBundle\RequestAwarePurger
     */
    protected $cacheStore;

    public function __construct(RequestAwarePurger $cacheStore)
    {
        $this->cacheStore = $cacheStore;
    }

    public function purge($tags)
    {
        if (empty($tags)) {
            return;
        }

        $tags = array_map(
            function ($tag) {
                return is_numeric($tag) ? 'location-' . $tag : $tag;
            },
            (array)$tags
        );

        $purgeRequest = Request::create('http://localhost/', 'PURGE');
        $purgeRequest->headers->set('key', implode(' ', $tags));
        $this->cacheStore->purgeByRequest($purgeRequest);
    }

    /**
     * @todo Adapt RequestAwarePurger to add a purgeAll method to avoid special requests like this known by tag storage.
     */
    public function purgeAll()
    {
        $purgeRequest = Request::create('http://localhost/', 'PURGE');
        $purgeRequest->headers->set('X-Location-Id', '*');
        $this->cacheStore->purgeByRequest($purgeRequest);
    }
}
