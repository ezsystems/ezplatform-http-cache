<?php
/**
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 */

namespace EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 *
 */
class KernelPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // slots
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($this->isSignalSlot($id) ||
                $this->isSmartCacheListener($id) ||
                $this->isCacheTagListener($id)
            ) {
                $container->removeDefinition($id);
            }
        }
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
        return preg_match('/ezpublish.cache_clear.content.[a-z_]]_listener/', $id);
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    protected function isCacheTagListener($id)
    {
        return $id === 'ezpublish.view.cache_response_listener';
    }
}
