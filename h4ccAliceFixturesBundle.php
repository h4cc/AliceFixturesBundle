<?php

namespace h4cc\AliceFixturesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

use h4cc\AliceFixturesBundle\DependencyInjection\Compiler\ProviderCompilerPass;
use h4cc\AliceFixturesBundle\DependencyInjection\Compiler\ProcessorCompilerPass;

class h4ccAliceFixturesBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ProviderCompilerPass());
        $container->addCompilerPass(new ProcessorCompilerPass());
    }
}
