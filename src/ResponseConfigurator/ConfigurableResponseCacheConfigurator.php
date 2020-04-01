<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\ResponseConfigurator;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * A ResponseCacheConfigurator configurable by constructor arguments.
 */
class ConfigurableResponseCacheConfigurator implements ResponseCacheConfigurator
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(ConfigResolverInterface $configResolver)
    {
        $this->configResolver = $configResolver;
    }

    public function enableCache(Response $response)
    {
        if ($this->isViewCachedEnabled()) {
            $response->setPublic();
        }

        return $this;
    }

    public function setSharedMaxAge(Response $response)
    {
        if ($this->isViewCachedEnabled() && $this->isTTLCacheEnabled() && !$response->headers->hasCacheControlDirective('s-maxage')) {
            $response->setSharedMaxAge($this->getDefaultTTL());
        }

        return $this;
    }

    private function isViewCachedEnabled(): bool
    {
        return $this->configResolver->getParameter('content.view_cache');
    }

    private function isTTLCacheEnabled(): bool
    {
        return $this->configResolver->getParameter('content.ttl_cache');
    }

    private function getDefaultTTL(): int
    {
        return (int)$this->configResolver->getParameter('content.default_ttl');
    }
}
