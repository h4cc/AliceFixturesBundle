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
 * @covers h4cc\AliceFixturesBundle\DependencyInjection\h4ccAliceFixturesExtension
 */
class h4ccAliceFixturesExtensionTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigWithDefaultValues()
    {
        $container = $this->getContainerWithLoadedExtension();

        $this->assertEquals(
            array('locale' => 'en_EN', 'seed' => 1, 'do_flush' => true),
            $container->getDefinition($container->getAlias('h4cc_alice_fixtures.manager'))->getArgument(0)
        );
    }


    /**
     * @dataProvider getPublicServiceIdProvider
     */
    public function testPublicServiceIds($publicServiceId)
    {
        $container = $this->getContainerWithLoadedExtension();
        $this->assertTrue($container->has($publicServiceId));
    }

    public function testLoadDefault()
    {
        $config = array(
            'locale' => 'de_DE',
            'seed' => 42,
            'do_flush' => true
        );

        $container = $this->getContainerWithLoadedExtension($config);

        $this->assertEquals(
            $config,
            $container->getDefinition($container->getAlias('h4cc_alice_fixtures.manager'))->getArgument(0)
        );
    }

    public function testLoadWithDefaultORM()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'doctrine' => 'orm',
        ));

        // Check that default service aliases are set.
        $this->assertEquals('h4cc_alice_fixtures.orm.schema_tool.doctrine', $container->getAlias('h4cc_alice_fixtures.orm.schema_tool'));
    }

    public function testLoadWithCustomORM()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'doctrine' => 'orm',
            'schema_tool' => 'my_schema_tool',
        ));

        // Check that the custom aliases are set.
        $this->assertEquals('my_schema_tool', $container->getAlias('h4cc_alice_fixtures.orm.schema_tool'));
    }

    public function testLoadWithDefaultMongoDbODM()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'doctrine' => 'mongodb-odm',
        ));

        // Check that default service aliases are set.
        $this->assertEquals('h4cc_alice_fixtures.orm.schema_tool.doctrine_mongodb', $container->getAlias('h4cc_alice_fixtures.orm.schema_tool'));
    }

    public function testLoadWithCustomMongoDbODM()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'doctrine' => 'mongodb-odm',
            'schema_tool' => 'my_schema_tool',
        ));

        // Check that the custom aliases are set.
        $this->assertEquals('my_schema_tool', $container->getAlias('h4cc_alice_fixtures.orm.schema_tool'));
    }

    public function testLoadManyManagersWithDefaultValuesAndWithoutDefaultManagerSet()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'managers' => array(
                'manager1' => array(
                    'doctrine' => 'orm',
                ),

                'manager2' => array(
                    'doctrine' => 'mongodb-odm',
                )
            )
        ));

        // Check that the custom aliases are set.
        $this->assertEquals(
            sprintf(h4ccAliceFixturesExtension::FIXTURE_MANAGER_NAME_MODEL, 'manager1'),
            $container->getAlias('h4cc_alice_fixtures.manager')
        );

        $this->assertEquals(
            'h4cc_alice_fixtures.orm.schema_tool.doctrine',
            $container->getAlias('h4cc_alice_fixtures.orm.schema_tool')
        );
    }

    public function testLoadManyManagersWithDefaultValuesAndWithDefaultManagerSet()
    {
        $container = $this->getContainerWithLoadedExtension(array(
            'managers' => array(
                'manager1' => array(
                    'doctrine' => 'orm',
                ),

                'manager2' => array(
                    'doctrine' => 'mongodb-odm',
                )
            ),
            'default_manager' => 'manager2'
        ));

        // Check that the custom aliases are set.
        $this->assertEquals(
            sprintf(h4ccAliceFixturesExtension::FIXTURE_MANAGER_NAME_MODEL, 'manager2'),
            $container->getAlias('h4cc_alice_fixtures.manager')
        );

        $this->assertEquals(
            'h4cc_alice_fixtures.orm.schema_tool.doctrine_mongodb',
            $container->getAlias('h4cc_alice_fixtures.orm.schema_tool')
        );
    }

    protected function getContainerWithLoadedExtension(array $config = array())
    {
        $container = new ContainerBuilder();

        $extension = new h4ccAliceFixturesExtension();
        $extension->load(array($config), $container);

        return $container;
    }

    public function getPublicServiceIdProvider()
    {
        return array(
            array('h4cc_alice_fixtures.loader.factory'),
            array('h4cc_alice_fixtures.manager'),
            array('h4cc_alice_fixtures.orm.schema_tool'),
            array('h4cc_alice_fixtures.orm.schema_tool.doctrine'),
        );
    }
}
