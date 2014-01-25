<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @author Julius Beckmann <github@h4cc.de>
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
          ->info('Global configuration, can be changed by each FixtureSet on its own.')
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
              ->scalarNode('object_manager')
                  ->defaultValue(null)
                  ->info('Define service id for the used object manager.')
              ->end()
              ->scalarNode('schema_tool')
                  ->defaultValue(null)
                  ->info('Define service id for the used Schema tool.')
              ->end()
              ->enumNode('doctrine')
                ->defaultValue('orm')
                ->values(array('orm', 'mongodb-odm'))
                ->info('This option enables selecting between Doctrine ORM and MongoDB ODM and will set the default object_manager and schema_tool services.')
              ->end()
          ->end();

        return $treeBuilder;
    }
}
