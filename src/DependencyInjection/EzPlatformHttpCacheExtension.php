<?php

namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection;

use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\Yaml\Yaml;

class EzPlatformHttpCacheExtension extends Extension implements PrependExtensionInterface
{
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
        $loader->load('slot.yml');
        $loader->load('view_cache.yml');
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
    }
}
