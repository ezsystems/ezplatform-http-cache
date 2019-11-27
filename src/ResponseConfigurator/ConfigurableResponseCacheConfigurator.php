<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\ResponseConfigurator;

use Symfony\Component\HttpFoundation\Response;

/**
 * A ResponseCacheConfigurator configurable by constructor arguments.
 */
class ConfigurableResponseCacheConfigurator implements ResponseCacheConfigurator
{
    /**
     * True if view cache is enabled, false if it is not.
     *
     * @var bool
     */
    private $enableViewCache;

    /**
     * True if TTL cache is enabled, false if it is not.
     *
     * @var bool
     */
    private $enableTtlCache;

    /**
     * Default ttl for ttl cache.
     *
     * @var int
     */
    private $defaultTtl;

    public function __construct($enableViewCache, $enableTtlCache, $defaultTtl)
    {
        $this->enableViewCache = $enableViewCache;
        $this->enableTtlCache = $enableTtlCache;
        $this->defaultTtl = $defaultTtl;
    }

    public function enableCache(Response $response)
    {
        if ($this->enableViewCache) {
            $response->setPublic();
        }

        return $this;
    }

    public function setSharedMaxAge(Response $response)
    {
        if ($this->enableViewCache && $this->enableTtlCache && !$response->headers->hasCacheControlDirective('s-maxage')) {
            $response->setSharedMaxAge($this->defaultTtl);
        }

        return $this;
    }
}
