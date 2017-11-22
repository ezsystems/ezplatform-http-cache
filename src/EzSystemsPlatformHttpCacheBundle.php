<?php

namespace EzSystems\PlatformHttpCacheBundle;

use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\ResponseTaggersPass;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\KernelPass;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\DriverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\EzPlatformHttpCacheExtension;

class EzSystemsPlatformHttpCacheBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ResponseTaggersPass());
        $container->addCompilerPass(new KernelPass());
        $container->addCompilerPass(new DriverPass());
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
                    throw new \LogicException(sprintf('Extension %s must implement Symfony\Component\DependencyInjection\Extension\ExtensionInterface.', get_class($extension)));
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
}
