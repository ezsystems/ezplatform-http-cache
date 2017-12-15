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

        if ($container->hasAlias('ezpublish.http_cache.purger')) {
            $container->removeAlias('ezpublish.http_cache.purger');
        }

        $this->symfonyPre34BC($container);
        $this->removeKernelRoleIdContextProvider($container);

        // Let's re-export purge_type setting so that driver's don't have to depend on kernel in order to acquire it
        $container->setParameter('ezplatform.http_cache.purge_type', $container->getParameter('ezpublish.http_cache.purge_type'));
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function symfonyPre34BC(ContainerBuilder $container)
    {
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
     * @param ContainerBuilder $container
     */
    protected function removeKernelRoleIdContextProvider(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('ezpublish.user.identity_definer.role_id')) {
            return;
        }

        $container->removeDefinition('ezpublish.user.identity_definer.role_id');

        // Also remove from arguments already passed to FOSHttpCache via compiler pass there.
        $arguments = $container->getDefinition('fos_http_cache.user_context.hash_generator')->getArguments();
        $arguments[0] = array_values(array_filter($arguments[0], function ($argument) {
            if ($argument === 'ezpublish.user.identity_definer.role_id') {
                return false;
            }

            return true;
        }));
        $container->getDefinition('fos_http_cache.user_context.hash_generator')->setArguments($arguments);
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
