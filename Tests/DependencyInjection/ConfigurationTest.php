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

use h4cc\AliceFixturesBundle\DependencyInjection\Configuration;

/**
 * Class ConfigurationTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $config = new Configuration();
        $tree = $config->getConfigTreeBuilder();

        $node = $tree->buildTree();
        $this->assertEquals('h4cc_alice_fixtures', $node->getName());

        /** @var $options \Symfony\Component\Config\Definition\ScalarNode[] */
        $options = $node->getChildren();
        $this->assertCount(6, $options);
        $this->assertEquals('en_EN', $options['locale']->getDefaultValue());
        $this->assertEquals(1, $options['seed']->getDefaultValue());
        $this->assertEquals(true, $options['do_flush']->getDefaultValue());
        $this->assertEquals(null, $options['object_manager']->getDefaultValue());
        $this->assertEquals(null, $options['schema_tool']->getDefaultValue());
        $this->assertEquals('orm', $options['doctrine']->getDefaultValue());
    }
}
