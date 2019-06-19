<?php

/**
 * File containing the HttpCachePass class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use InvalidArgumentException;

/**
 * HttpCache related compiler pass.
 *
 * Ensures Varnish proxy client is correctly configured.
 */
class HttpCachePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $this->processHttpDispatcher($container);
        $this->processVarnishProxyClient($container);
    }

    private function processHttpDispatcher(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fos_http_cache.proxy_client.varnish.http_dispatcher')) {
            return;
        }

        $httpDispatcher = $container->findDefinition('fos_http_cache.proxy_client.varnish.http_dispatcher');
        $httpDispatcher->replaceArgument(0, []);
    }

    private function processVarnishProxyClient(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fos_http_cache.proxy_client.varnish')) {
            throw new InvalidArgumentException('Varnish proxy client must be enabled in FOSHttpCacheBundle');
        }

        $fosConfig = array_merge(...$container->getExtensionConfig('fos_http_cache'));

        $servers = $fosConfig['proxy_client']['varnish']['http']['servers'] ?? [];
        $baseUrl = $fosConfig['proxy_client']['varnish']['http']['base_url'] ?? '';

        $container->setParameter(
            'ezplatform.http_cache.varnish.http.servers',
            $servers
        );

        $container->setParameter(
            'ezplatform.http_cache.varnish.http.base_url',
            $baseUrl
        );
    }
}
