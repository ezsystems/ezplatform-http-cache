<?php

namespace spec\EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler;

use EzSystems\PlatformHttpCacheBundle\DependencyInjection\Compiler\KernelPass;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class KernelPassSpec extends ObjectBehavior
{
    function let(ContainerBuilder $container)
    {
        $container->getDefinitions()->willReturn([]);
        $container->getAlias('ezpublish.http_cache.purge_client')->willReturn('some_random_id');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(KernelPass::class);
    }

    function it_disables_the_kernels_httpcache_slots(ContainerBuilder $container)
    {
        $container->getDefinitions()->willReturn([
            'ezpublish.http_cache.witness_service' => new Definition(),
            'ezpublish.http_cache.signalslot.some_slot' => new Definition(),
            'ezpublish.http_cache.signalslot.some_other_slot' => new Definition(),
            'witness_service' => new Definition(),
        ]);
        $container->removeDefinition('ezpublish.http_cache.signalslot.some_slot')->shouldBeCalled();
        $container->removeDefinition('ezpublish.http_cache.signalslot.some_other_slot')->shouldBeCalled();

        $this->process($container);
    }

    function it_disables_the_kernels_smartcache_event_listeners(ContainerBuilder $container)
    {
        $container->getDefinitions()->willReturn([
            'ezpublish.cache_clear.content.some_listener' => new Definition(),
            'witness_service' => new Definition(),
        ]);
        $container->removeDefinition('ezpublish.cache_clear.content.some_listener')->shouldBeCalled();

        $this->process($container);
    }

    function it_disables_the_kernels_view_cache_response_listener(ContainerBuilder $container)
    {
        $container->getDefinitions()->willReturn([
            'ezpublish.view.cache_response_listener' => new Definition(),
            'witness_service' => new Definition(),
        ]);
        $container->removeDefinition('ezpublish.view.cache_response_listener')->shouldBeCalled();

        $this->process($container);
    }
}
