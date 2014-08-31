<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests\Loader;

use h4cc\AliceFixturesBundle\Loader\Factory;

/**
 * Class FactoryTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\Loader\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    public function setUp()
    {
        $this->factory = new Factory();
    }

    public function testGetLoader()
    {
        $loader = $this->factory->getLoader('yaml', 'de_DE');

        $this->assertInstanceOf('\Nelmio\Alice\Loader\Yaml', $loader);

        $loader = $this->factory->getLoader('php', 'de_DE');

        $this->assertInstanceOf('\Nelmio\Alice\Loader\Base', $loader);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unknown loader type 'foo'.
     */
    public function testException()
    {
        $this->factory->getLoader('foo', 'bar');
    }
}
