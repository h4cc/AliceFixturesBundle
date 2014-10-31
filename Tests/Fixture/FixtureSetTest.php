<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests\Fixtures;

use h4cc\AliceFixturesBundle\Fixtures\FixtureSet;

/**
 * Class FixtureSetTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\Fixtures\FixtureSet
 */
class FixtureSetTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        $set = new FixtureSet();

        $this->assertEquals('en_US', $set->getLocale());
        $this->assertEquals(1, $set->getSeed());
        $this->assertEquals(false, $set->getDoDrop());
        $this->assertEquals(true, $set->getDoPersist());
        $this->assertEquals(array(), $set->getFiles());
    }

    public function testConstructor()
    {
        $set = new FixtureSet(
            array(
                'locale' => 'de_DE',
                'seed' => 42,
                'do_drop' => true,
                'do_persist' => false,
                'order' => 1337
            )
        );

        $this->assertEquals('de_DE', $set->getLocale());
        $this->assertEquals(42, $set->getSeed());
        $this->assertEquals(1337, $set->getOrder());
        $this->assertEquals(true, $set->getDoDrop());
        $this->assertEquals(false, $set->getDoPersist());
        $this->assertEquals(array(), $set->getFiles());
    }

    public function testSetter()
    {
        $set = new FixtureSet();

        $set->setLocale('de_DE');
        $set->setSeed(42);
        $set->setOrder(1337);
        $set->setDoDrop(true);
        $set->setDoPersist(false);
        $set->addFile('/foo', 'bar');
        $set->addFile('/bob', 'xyz');

        $this->assertEquals('de_DE', $set->getLocale());
        $this->assertEquals(42, $set->getSeed());
        $this->assertEquals(1337, $set->getOrder());
        $this->assertEquals(true, $set->getDoDrop());
        $this->assertEquals(false, $set->getDoPersist());
        $this->assertEquals(
            array(
                0 => array('type' => "bar", 'path' => "/foo"),
                1 => array('type' => "xyz", 'path' => "/bob"),
            ),
            $set->getFiles()
        );
    }
}
