<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection;

use eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface;
use FOS\HttpCache\TagHeaderFormatter\TagHeaderFormatter;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

class EzPlatformHttpCacheExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\Configuration\ParserInterface[]
     */
    private $extraConfigParsers = [];

    public function getAlias()
    {
        return 'ez_platform_http_cache';
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('event.yml');
        $loader->load('view_cache.yml');

        $this->setDefaultResponseHeader($container);
        $this->setDefaultTagSeparator($container);
    }

    public function prepend(ContainerBuilder $container)
    {
        // Load params early as we use them in below
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('default_settings.yml');

        // Override default settings for FOSHttpCacheBundle
        $configFile = __DIR__ . '/../Resources/config/fos_http_cache.yml';
        $config = Yaml::parse(file_get_contents($configFile));
        $container->prependExtensionConfig('fos_http_cache', $config);
        $container->addResource(new FileResource($configFile));

        // Override Core views
        $coreExtensionConfigFile = realpath(__DIR__ . '/../Resources/config/prepend/ezpublish.yml');
        $container->prependExtensionConfig('ezpublish', Yaml::parseFile($coreExtensionConfigFile));
        $container->addResource(new FileResource($coreExtensionConfigFile));
    }

    public function addExtraConfigParser(ParserInterface $configParser)
    {
        $this->extraConfigParsers[] = $configParser;
    }

    public function getExtraConfigParsers()
    {
        return $this->extraConfigParsers;
    }

    /**
     * Overrides default header name based on setting tag_mode in FosHttpCacheBundle configuration.
     */
    private function setDefaultResponseHeader(ContainerBuilder $container): void
    {
        $purgeType = $container->getParameter('ezpublish.http_cache.purge_type');

        $responseHeader = 'http' === $purgeType ? 'xkey' : TagHeaderFormatter::DEFAULT_HEADER_NAME;

        $container->setParameter('fos_http_cache.tag_handler.response_header', $responseHeader);
    }

    /**
     * Overrides default header separator based on setting tag_mode in FosHttpCacheBundle configuration.
     */
    private function setDefaultTagSeparator(ContainerBuilder $container): void
    {
        $purgeType = $container->getParameter('ezpublish.http_cache.purge_type');

        $separator = 'http' === $purgeType ? ' ' : ',';

        $container->setParameter('fos_http_cache.tag_handler.separator', $separator);
    }
}
