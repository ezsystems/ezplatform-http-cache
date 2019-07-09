<?php

/**
 * File containing the HttpCachePass class.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler;

use EzSystems\PlatformHttpCacheBundle\ProxyClient\HttpDispatcherFactory;
use FOS\HttpCache\ProxyClient\HttpDispatcher;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

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
    }

    private function processHttpDispatcher(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('fos_http_cache.proxy_client.varnish')) {
            throw new InvalidArgumentException('Varnish proxy client must be enabled in FOSHttpCacheBundle');
        }
        // Override FOS default httpDispatcher with \EzSystems\PlatformHttpCacheBundle\ProxyClient\HttpDispatcherFactory.
        $container->removeDefinition('fos_http_cache.proxy_client.varnish.http_dispatcher');

        $fosConfig = array_merge(...$container->getExtensionConfig('fos_http_cache'));

        $servers = $fosConfig['proxy_client']['varnish']['http']['servers'] ?? [];
        $baseUrl = $fosConfig['proxy_client']['varnish']['http']['base_url'] ?? '';

        $definition = new Definition(HttpDispatcher::class);
        $definition->setFactory([
            new Reference(HttpDispatcherFactory::class),
            'buildHttpDispatcher',
        ]);
        $definition->setLazy(true);
        $definition->setArguments([
            $servers,
            $baseUrl,
        ]);
        $container->setDefinition(
            'fos_http_cache.proxy_client.varnish.http_dispatcher',
            $definition
        );
    }
}
