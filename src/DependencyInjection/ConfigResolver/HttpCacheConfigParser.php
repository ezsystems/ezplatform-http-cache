<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection\ConfigResolver;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface;
use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\SiteAccessAware\ContextualizerInterface;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

class HttpCacheConfigParser implements ParserInterface
{
    /**
     * @var ExtensionInterface
     */
    private $httpCacheExtension;

    public function __construct(ExtensionInterface $httpCacheExtension)
    {
        $this->httpCacheExtension = $httpCacheExtension;
    }

    public function addSemanticConfig(NodeBuilder $nodeBuilder)
    {
        $subBuilder = $nodeBuilder
            ->arrayNode('http_cache')
                ->info('Settings related to Http cache')
                ->children()
                    ->arrayNode('purge_servers')
                        ->info('Servers to use for Http PURGE (will NOT be used if ezpublish.http_cache.purge_type is "local").')
                        ->example(array('http://localhost/', 'http://another.server/'))
                        ->requiresAtLeastOneElement()
                        ->prototype('scalar')->end()
                    ->end()
                    ->scalarNode('varnish_invalidate_token_name')
                        ->info('Optional: Varnish Invalidation token name for purge')
                        ->defaultValue('x-purge-token')
                    ->end()
                    ->scalarNode('varnish_invalidate_token')
                        ->info('Optional: Varnish Invalidation token for purge')
                        ->defaultNull()
                    ->end();

        foreach ($this->getExtraConfigParsers() as $extraConfigParser) {
            $extraConfigParser->addSemanticConfig($subBuilder);
        }

        $nodeBuilder->end()->end();
    }

    public function mapConfig(array &$scopeSettings, $currentScope, ContextualizerInterface $contextualizer)
    {
        if (!isset($scopeSettings['http_cache'])) {
            return;
        }

        if (isset($scopeSettings['http_cache']['purge_servers'])) {
            $contextualizer->setContextualParameter('http_cache.purge_servers', $currentScope, $scopeSettings['http_cache']['purge_servers']);
        }

        if (isset($scopeSettings['http_cache']['varnish_invalidate_token_name'])) {
            $contextualizer->setContextualParameter('http_cache.varnish_invalidate_token_name', $currentScope, $scopeSettings['http_cache']['varnish_invalidate_token_name']);
        }

        if (isset($scopeSettings['http_cache']['varnish_invalidate_token'])) {
            $contextualizer->setContextualParameter('http_cache.varnish_invalidate_token', $currentScope, $scopeSettings['http_cache']['varnish_invalidate_token']);
        }

        foreach ($this->getExtraConfigParsers() as $extraConfigParser) {
            $extraConfigParser->mapConfig($scopeSettings['http_cache'], $currentScope, $contextualizer);
        }
    }

    public function preMap(array $config, ContextualizerInterface $contextualizer)
    {
        if (!isset($config['http_cache'])) {
            return;
        }

        foreach ($this->getExtraConfigParsers() as $extraConfigParser) {
            $extraConfigParser->preMap($config['http_cache'], $contextualizer);
        }
    }

    public function postMap(array $config, ContextualizerInterface $contextualizer)
    {
        if (!isset($config['http_cache'])) {
            return;
        }

        foreach ($this->getExtraConfigParsers() as $extraConfigParser) {
            $extraConfigParser->postMap($config['http_cache'], $contextualizer);
        }
    }

    /**
     * @return ParserInterface[]
     */
    private function getExtraConfigParsers()
    {
        return $this->httpCacheExtension->getExtraConfigParsers();
    }
}
