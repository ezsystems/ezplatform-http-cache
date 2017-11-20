<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use FOS\HttpCacheBundle\CacheManager;

/**
 * Purge client based on FOSHttpCacheBundle.
 */
class VarnishPurgeClient implements PurgeClientInterface
{
    /**
     * @var \FOS\HttpCacheBundle\CacheManager
     */
    private $cacheManager;

    /**
     * @var string
     */
    private $purgeHeader;

    /**
     * @var bool
     */
    private $onePurgePerTag;

    /**
     * VarnishPurgeClient constructor.
     * @param CacheManager $cacheManager
     * @param string $purgeHeader
     * @param bool $onePurgePerTag
     */
    public function __construct(CacheManager $cacheManager, $purgeHeader = 'key', $onePurgePerTag = false)
    {
        $this->cacheManager = $cacheManager;
        $this->purgeHeader = $purgeHeader;
        $this->onePurgePerTag = $onePurgePerTag;
    }

    public function __destruct()
    {
        $this->cacheManager->flush();
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

        // We either send all tags or one by one to FOS\HttpCache\ProxyClient\Varnish que & handled on kernel.terminate.
        if (!$this->onePurgePerTag) {
            return $this->cacheManager->invalidatePath(
                '/',
                [$this->purgeHeader => $tags, 'Host' => empty($_SERVER['SERVER_NAME']) ? 'localhost' : $_SERVER['SERVER_NAME']]
            );
        }

        foreach ($tags as $tag) {
            $this->cacheManager->invalidatePath(
                '/',
                [$this->purgeHeader => $tag, 'Host' => empty($_SERVER['SERVER_NAME']) ? 'localhost' : $_SERVER['SERVER_NAME']]
            );
        }
    }

    public function purgeAll()
    {
        $this->cacheManager->invalidate(['key' => '.*']);
    }

    /**
     * @internal Only for use by tests.
     */
    public function enableOnePurgePerTag($state = true)
    {
        $this->onePurgePerTag = $state;
    }
}
