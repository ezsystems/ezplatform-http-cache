<?php

namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class PurgeTypeSettingsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $container->removeAlias('ezpublish.http_cache.purge_client');

        $purgeType = $container->getParameter('ezpublish.http_cache.purge_type');
        $purgeService = null;

        $purgeClientServiceIds = $container->findTaggedServiceIds('ezplatform.http_cache.purge_client');
        foreach ($purgeClientServiceIds as $purgeClientServiceId => $attributes) {
            $hasPurgeTypeAttribute = false;
            foreach ($attributes as $attribute) {
                if (array_key_exists('purge_type', $attribute)) {
                    $hasPurgeTypeAttribute = true;
                    if ($purgeType === $attribute['purge_type']) {
                        $purgeService = $purgeClientServiceId;
                        break;
                    }
                }
            }
            if (!$hasPurgeTypeAttribute) {
                throw new \InvalidArgumentException("Missing attribute 'purge_type' in tagged service '$purgeClientServiceId'.");
            }
        }

        if ($purgeService ===  null) {
            throw new \InvalidArgumentException("No driver found being able to handle purge_type '$purgeType'.");
        }

        $container->setAlias('ezplatform.http_cache.purge_client', $purgeService);
    }
}
