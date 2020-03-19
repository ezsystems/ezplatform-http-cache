<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
declare(strict_types=1);

namespace EzSystems\PlatformHttpCacheBundle\ProxyClient;

use eZ\Publish\Core\MVC\ConfigResolverInterface;
use EzSystems\PlatformHttpCacheBundle\Controller\InvalidateTokenController;
use FOS\HttpCache\ProxyClient\Dispatcher;
use FOS\HttpCache\ProxyClient\Invalidation\BanCapable;
use FOS\HttpCache\ProxyClient\Invalidation\PurgeCapable;
use FOS\HttpCache\ProxyClient\Invalidation\RefreshCapable;
use FOS\HttpCache\ProxyClient\Invalidation\TagCapable;
use FOS\HttpCache\ProxyClient\Varnish as FosVarnish;
use Http\Message\RequestFactory;

final class Varnish extends FosVarnish implements BanCapable, PurgeCapable, RefreshCapable, TagCapable
{
    /** @var \eZ\Publish\Core\MVC\ConfigResolverInterface */
    private $configResolver;

    public function __construct(
        ConfigResolverInterface $configResolver,
        Dispatcher $httpDispatcher,
        array $options = [],
        RequestFactory $messageFactory = null
    ) {
        parent::__construct($httpDispatcher, $options, $messageFactory);
        $this->configResolver = $configResolver;
    }

    private function fetchAndMergeAuthHeaders($headers): array
    {
        if ($this->configResolver->hasParameter('http_cache.varnish_invalidate_token')) {
            $headers[InvalidateTokenController::TOKEN_HEADER_NAME] = $this->configResolver->getParameter('http_cache.varnish_invalidate_token');
        }

        return $headers;
    }

    protected function queueRequest($method, $url, array $headers, $validateHost = true, $body = null)
    {
        parent::queueRequest($method, $url, $this->fetchAndMergeAuthHeaders($headers), $body);
    }
}
