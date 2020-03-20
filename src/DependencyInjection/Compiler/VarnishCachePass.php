<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler;

use EzSystems\PlatformHttpCacheBundle\ProxyClient\Varnish;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class VarnishCachePass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fos_http_cache.proxy_client.varnish')) {
            $container->removeDefinition('ezplatform.http_cache.proxy_client.varnish.http_dispatcher');
            return;
        }

        $this->overrideDefaultProxyClient($container);
        $this->processVarnishProxyClientSettings($container);
    }

    private function processVarnishProxyClientSettings(ContainerBuilder $container): void
    {
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

    private function overrideDefaultProxyClient(ContainerBuilder $container): void
    {
        $varnishProxyClient = $container->getDefinition('fos_http_cache.proxy_client.varnish');
        $varnishProxyClient->setClass(Varnish::class);
        $varnishProxyClient->setArguments([
            new Reference('ezpublish.config.resolver'),
            new Reference('fos_http_cache.proxy_client.varnish.http_dispatcher'),
            $container->getParameter('fos_http_cache.proxy_client.varnish.options'),
        ]);
    }
}
