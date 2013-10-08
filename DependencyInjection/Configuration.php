<?php

namespace h4cc\AliceFixturesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('h4cc_alice_fixtures');

        $rootNode
            ->children()
                ->scalarNode('locale')
                    ->defaultValue('en_EN')
                    ->info('Locale which will be used by faker for randomized data.')
                ->end()
                ->scalarNode('seed')
                    ->defaultValue(1)
                    ->info('A seed to make sure Faker generates data consistently across runs, set to "null" to disable.')
                ->end()
                ->scalarNode('do_flush')
                    ->defaultValue(true)
                    ->info('Set to false if no ORM flushes should be made.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}