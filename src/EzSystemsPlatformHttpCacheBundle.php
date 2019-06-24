<?php

/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle;

use EzSystems\PlatformHttpCacheBundle\DependencyInjection\ConfigResolver\HttpCacheConfigParser;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\HttpCachePass;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\ResponseTaggersPass;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\KernelPass;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\DriverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsPlatformHttpCacheBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ResponseTaggersPass());
        $container->addCompilerPass(new KernelPass());
        $container->addCompilerPass(new DriverPass());
        $container->addCompilerPass(new HttpCachePass());

        $this->registerConfigParser($container);
    }

    public function getContainerExtensionClass()
    {
        return 'EzSystems\PlatformHttpCacheBundle\DependencyInjection\EzPlatformHttpCacheExtension';
    }

    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $extension = $this->createContainerExtension();

            if (null !== $extension) {
                if (!$extension instanceof ExtensionInterface) {
                    throw new \LogicException(sprintf('Extension %s must implement Symfony\Component\DependencyInjection\Extension\ExtensionInterface.', \get_class($extension)));
                }
                $this->extension = $extension;
            } else {
                $this->extension = false;
            }
        }

        if ($this->extension) {
            return $this->extension;
        }
    }

    public function registerConfigParser(ContainerBuilder $container)
    {
        /** @var \eZ\Bundle\EzPublishCoreBundle\DependencyInjection\EzPublishCoreExtension $eZExtension */
        $eZExtension = $container->getExtension('ezpublish');
        $eZExtension->addConfigParser(
            new HttpCacheConfigParser(
                $container->getExtension('ez_platform_http_cache')
            )
        );
    }
}
