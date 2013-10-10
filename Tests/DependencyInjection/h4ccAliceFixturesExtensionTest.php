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
    public function testLoad()
    {
        $container = new ContainerBuilder();

        $config = array(
            'locale' => 'de_DE',
            'seed' => 42,
            'do_flush' => true,
        );

        $extension = new h4ccAliceFixturesExtension();
        $extension->load(array($config), $container);

        $this->assertEquals($config, $container->getDefinition('h4cc_alice_fixtures.manager')->getArgument(0));

        $publicServiceIds = array(
            'h4cc_alice_fixtures.loader.factory',
            'h4cc_alice_fixtures.manager',
            'h4cc_alice_fixtures.orm.schema_tool',
        );

        foreach ($publicServiceIds as $id) {
            $this->assertTrue($container->has($id));
        }
    }
}
