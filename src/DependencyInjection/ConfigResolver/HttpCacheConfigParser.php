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
                    ->scalarNode('purge_auth_header')
                        ->info('Header name for authenticated purge')
                        ->defaultValue('X-PURGE-AUTH')
                    ->end()
                    ->scalarNode('purge_auth_key')
                        ->info('Purge authentication key')
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

        if (isset($scopeSettings['http_cache']['purge_auth_header'])) {
            $contextualizer->setContextualParameter('http_cache.purge_auth_header', $currentScope, $scopeSettings['http_cache']['purge_auth_header']);
        }

        if (isset($scopeSettings['http_cache']['purge_auth_key'])) {
            $contextualizer->setContextualParameter('http_cache.purge_auth_key', $currentScope, $scopeSettings['http_cache']['purge_auth_key']);
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
