<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */
namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Disables some of the http-cache services declared by the kernel so that
 * they can be replaced with this bundle's.
 */
class KernelPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($this->isSignalSlot($id) ||
                $this->isSmartCacheListener($id) ||
                $this->isResponseCacheListener($id) ||
                $this->isCachePurger($id)
            ) {
                $container->removeDefinition($id);
            }
        }
        $container->removeAlias('ezpublish.http_cache.purger');
        $arguments = $container->getDefinition('cache_clearer')->getArguments();

        // BC Symfony < 3.4, as of 3.4 and up handles this itself, on lower versions we need to adjust the arguments manually
        if (!is_array($arguments[0])) {
            return;
        }

        $arguments[0] = array_values(array_filter($arguments[0], function ($argument) {
            if ($this->isCachePurger($argument)) {
                return false;
            }

            return true;
        }));
        $container->getDefinition('cache_clearer')->setArguments($arguments);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    protected function isSignalSlot($id)
    {
        return strpos($id, 'ezpublish.http_cache.signalslot.') === 0;
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    protected function isSmartCacheListener($id)
    {
        return preg_match('/^ezpublish\.cache_clear\.content.[a-z_]+_listener/', $id);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    protected function isResponseCacheListener($id)
    {
        return $id === 'ezpublish.view.cache_response_listener';
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    protected function isCachePurger($id)
    {
        return strpos($id, 'ezpublish.http_cache.purger.') === 0;
    }
}
