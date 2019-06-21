<?php

/**
 * File containing the LocalPurgeClient class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use Toflar\Psr6HttpCacheStore\Psr6StoreInterface;

/**
 * LocalPurgeClient emulates an Http PURGE request to be received by the Proxy Tag cache store.
 * Handy for single-serve using Symfony Proxy..
 */
class LocalPurgeClient implements PurgeClientInterface
{
    /** @var \Toflar\Psr6HttpCacheStore\Psr6StoreInterface */
    protected $cacheStore;

    public function __construct(Psr6StoreInterface $cacheStore)
    {
        $this->cacheStore = $cacheStore;
    }

    public function purge($tags)
    {
        $this->cacheStore->invalidateTags($tags);
    }

    public function purgeAll()
    {
        $this->cacheStore->invalidateTags(['ez-all']);
    }
}
