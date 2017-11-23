<?php
/**
 * Created by PhpStorm.
 * User: bdunogier
 * Date: 23/11/2017
 * Time: 12:06
 */

namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection\ConfigResolver;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;

class HttpCacheConfigParser implements ParserInterface
{
    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $nodeBuilder
            ->arrayNode('http_cache')
                ->info('Settings related to Http cache')
                ->children()
                    ->arrayNode('purge_servers')
                        ->info('THIS IS FROM EZPLATFORM-HTTP-CACHE ! Servers to use for Http PURGE (will NOT be used if ezpublish.http_cache.purge_type is "local").')
                        ->example(array('http://localhost/', 'http://another.server/'))
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')->end()
                    ->end()
                ->end()
            ->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (isset($scopeSettings['http_cache']['purge_servers'])) {
            $contextualizer->setContextualParameter('http_cache.purge_servers', $currentScope, $scopeSettings['http_cache']['purge_servers']);
        }
    }

    public function preMap(array $config, ContextualizerInterface $contextualizer)
    {
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
    }
}