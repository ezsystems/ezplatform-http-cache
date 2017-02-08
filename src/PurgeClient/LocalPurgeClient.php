<?php

/**
 * File containing the LocalPurgeClient class.
 *
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
     * Can we find a way to NOT implement this method ?
     * PurgeClientInterface is defined in eZ/Publish/Core/MVC/Symfony/Cache/Http, and purgeAll() is defined in it,
     * but deprecated by the multi-tagging implementation.
     *
     * Could we add PurgeClientInterface to HttpCacheBundle, and remove purgeAll from this version ?
     * How would this work with the current implementation ?
     */
    public function purgeAll()
    {
        $this->cacheStore->purgeAllContent();
    }
}
