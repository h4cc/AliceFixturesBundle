<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests\DependencyInjection;

use h4cc\AliceFixturesBundle\DependencyInjection\h4ccAliceFixturesExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class h4ccAliceFixturesExtensionTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class h4ccAliceFixturesExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testPublicServiceIds()
    {
        $container = $this->getContainerWithLoadedExtension();

        $publicServiceIds = array(
            'h4cc_alice_fixtures.loader.factory',
            'h4cc_alice_fixtures.manager',
            'h4cc_alice_fixtures.orm.schema_tool',
            'h4cc_alice_fixtures.orm.schema_tool.doctrine',
            'h4cc_alice_fixtures.orm.schema_tool.mongodb',
            'h4cc_alice_fixtures.object_manager',
        );

        foreach ($publicServiceIds as $id) {
            $this->assertTrue($container->has($id));
        }
    }

    public function testLoadDefault()
    {
        $config = array(
            'locale' => 'de_DE',
            'seed' => 42,
            'do_flush' => true
        );

        $container = $this->getContainerWithLoadedExtension($config);

        $this->assertEquals($config, $container->getDefinition('h4cc_alice_fixtures.manager')->getArgument(0));
    }

    public function testLoadWithDefaultORM()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'doctrine' => 'orm',
        ));

        // Check that default service aliases are set.
        $this->assertEquals('doctrine.orm.entity_manager', $container->getAlias('h4cc_alice_fixtures.object_manager'));
        $this->assertEquals('h4cc_alice_fixtures.orm.schema_tool.doctrine', $container->getAlias('h4cc_alice_fixtures.orm.schema_tool'));
    }

    public function testLoadWithCustomORM()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'doctrine' => 'orm',
            'object_manager' => 'my_object_manager',
            'schema_tool' => 'my_schema_tool',
        ));

        // Check that the custom aliases are set.
        $this->assertEquals('my_object_manager', $container->getAlias('h4cc_alice_fixtures.object_manager'));
        $this->assertEquals('my_schema_tool', $container->getAlias('h4cc_alice_fixtures.orm.schema_tool'));
    }

    public function testLoadWithDefaultMongoDbODM()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'doctrine' => 'mongodb-odm',
        ));

        // Check that default service aliases are set.
        $this->assertEquals('doctrine_mongodb.odm.document_manager', $container->getAlias('h4cc_alice_fixtures.object_manager'));
        $this->assertEquals('h4cc_alice_fixtures.orm.schema_tool.mongodb', $container->getAlias('h4cc_alice_fixtures.orm.schema_tool'));
    }

    public function testLoadWithCustomMongoDbODM()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'doctrine' => 'mongodb-odm',
            'object_manager' => 'my_object_manager',
            'schema_tool' => 'my_schema_tool',
        ));

        // Check that the custom aliases are set.
        $this->assertEquals('my_object_manager', $container->getAlias('h4cc_alice_fixtures.object_manager'));
        $this->assertEquals('my_schema_tool', $container->getAlias('h4cc_alice_fixtures.orm.schema_tool'));
    }

    protected function getContainerWithLoadedExtension(array $config = array())
    {
        $container = new ContainerBuilder();

        $extension = new h4ccAliceFixturesExtension();
        $extension->load(array($config), $container);

        return $container;
    }
}
