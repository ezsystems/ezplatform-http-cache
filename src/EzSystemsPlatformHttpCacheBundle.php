<?php

namespace EzSystems\PlatformHttpCacheBundle;

use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\ResponseTaggersPass;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\KernelPass;
use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\DriverPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class EzSystemsPlatformHttpCacheBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ResponseTaggersPass());
        $container->addCompilerPass(new KernelPass());
        $container->addCompilerPass(new DriverPass());
    }
}
