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
        $this->processCacheManager($container);
        $this->setProxyClient($container);
    }

    private function processCacheManager(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezplatform.http_cache.cache_manager')) {
            return;
        }

        // Remove so 7.0 & 6.x kernels skip HttpCachePass
        $container->removeAlias('ezpublish.http_cache.cache_manager');

        if (!$container->hasDefinition('fos_http_cache.proxy_client.varnish')) {
            throw new InvalidArgumentException('Varnish proxy client must be enabled in FOSHttpCacheBundle');
        }

        $varnishClientDef = $container->findDefinition('fos_http_cache.proxy_client.varnish');
        $varnishClientDef->setFactory(
            [
                new Reference('ezplatform.http_cache.proxy_client.varnish.factory'),
                'buildProxyClient',
            ]
        );
        // Set it lazy as it can be loaded during cache warming and factory depends on ConfigResolver while cache warming
        // occurs before SA matching.
        $varnishClientDef->setLazy(true);

        // Forcing cache manager to use Varnish proxy client, for PURGE/BAN support.
        $cacheManagerDef = $container->findDefinition('ezplatform.http_cache.cache_manager');
        $cacheManagerDef->replaceArgument(0, new Reference('fos_http_cache.proxy_client.varnish'));
    }

    public function setProxyClient(ContainerBuilder $container)
    {
        // In 1.13, ezpublish-kernel will inject the fos proxy client into the VarnishProxyClientFactory
        // We need to inject our own ProxyClient
        if ($container->hasDefinition('ezpublish.http_cache.proxy_client.varnish.factory')) {
            $def = $container->getDefinition('ezpublish.http_cache.proxy_client.varnish.factory');
            $arguments = $def->getArguments();
            if (count($arguments) == 3) {
                $proxyClientClassArg = $arguments[2];
                if ($proxyClientClassArg === '%fos_http_cache.proxy_client.varnish.class%') {
                    $arguments[2] = '%ezplatform.http_cache.proxy_client.varnish.class%';
                    $def->setArguments($arguments);
                }
            }
        }
    }
}
