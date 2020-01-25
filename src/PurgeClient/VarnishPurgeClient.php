<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\PurgeClient;

use FOS\HttpCacheBundle\CacheManager;
use eZ\Publish\Core\MVC\ConfigResolverInterface;

/**
 * Purge client based on FOSHttpCacheBundle.
 */
class VarnishPurgeClient implements PurgeClientInterface
{
    const INVALIDATE_TOKEN_PARAM = 'http_cache.varnish_invalidate_token';
    const INVALIDATE_TOKEN_PARAM_NAME = 'x-invalidate-token';
    const DEFAULT_HEADER_LENGTH = 7500;
    const XKEY_TAG_SEPERATOR = ' ';

    /**
     * @var \FOS\HttpCacheBundle\CacheManager
     */
    private $cacheManager;

    /**
     * @var \eZ\Publish\Core\MVC\ConfigResolverInterface
     */
    private $configResolver;

    public function __construct(CacheManager $cacheManager, ConfigResolverInterface $configResolver)
    {
        $this->cacheManager = $cacheManager;
        $this->configResolver = $configResolver;
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

        // For 5.4/1.x BC make sure to map any int to location id tag
        $tags = array_unique(array_map(static function ($tag) {
            return is_numeric($tag) ? 'l' . $tag : $tag;
        },
            (array)$tags
        ));

        $headers = $this->getPurgeHeaders();
        $chunkSize = $this->determineTagsPerHeader($tags);

        // NB! This requires varnish-modules 0.10.2, if you need support for varnish-modules 0.9.x, use ezplatform-http-cache 0.8.x
        foreach (array_chunk($tags, $chunkSize) as $tagchunk) {
            $headers['key'] = implode(' ', $tagchunk);
            $this->cacheManager->invalidatePath(
                '/',
                $headers
            );
        }
    }

    public function purgeAll()
    {
        $headers = $this->getPurgeHeaders();
        $headers['key'] = 'ez-all';

        $this->cacheManager->invalidatePath(
            '/',
            $headers
        );
    }

    /**
     * Adds an generic headers needed for purge (Host and Authentication).
     *
     * @return array
     */
    private function getPurgeHeaders()
    {
        $headers = [
            'Host' => empty($_SERVER['SERVER_NAME']) ? parse_url($this->configResolver->getParameter('http_cache.purge_servers')[0], PHP_URL_HOST) : $_SERVER['SERVER_NAME'],
        ];

        if ($this->configResolver->hasParameter(self::INVALIDATE_TOKEN_PARAM)
            && null !== ($token = $this->configResolver->getParameter(self::INVALIDATE_TOKEN_PARAM))
        ) {
            $headers[self::INVALIDATE_TOKEN_PARAM_NAME] = $token;
        }

        return $headers;
    }

    /**
     * Get amount of tags per header, adapted from FOSHttpCache 2.x.
     *
     * @param array $tags
     *
     * @return int
     */
    private function determineTagsPerHeader(array $tags)
    {
        if (mb_strlen(implode(self::XKEY_TAG_SEPERATOR, $tags)) < self::DEFAULT_HEADER_LENGTH) {
            return count($tags);
        }

        // Estimate the amount of tags by dividing the max header length by the largest tag (minus the glue length)
        $tagsize = max(array_map('mb_strlen', $tags));

        return floor(self::DEFAULT_HEADER_LENGTH / ($tagsize + strlen(self::XKEY_TAG_SEPERATOR))) ?: 1;
    }
}
