<?php

namespace h4cc\AliceFixturesBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class ProcessorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('h4cc_alice_fixtures.manager')) {
            return;
        }

        $definition = $container->getDefinition('h4cc_alice_fixtures.manager');

        foreach ($container->findTaggedServiceIds('h4cc_alice_fixtures.processor') as $id => $attributes) {
            $definition->addMethodCall('addProcessor', array(new Reference($id)));
        }
    }
}
