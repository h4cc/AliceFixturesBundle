<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests;

use h4cc\AliceFixturesBundle\ORM\Doctrine;

/**
 * Class DoctrineTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\ORM\Doctrine
 */
class DoctrineTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $managerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $managerRegistryMock;

    private $objects = array();
    private $testObject;

    public function setUp()
    {
        $this->managerMock = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->setMethods(array('persist', 'find', 'remove', 'merge', 'detach', 'flush'))
            ->getMockForAbstractClass();

        $this->managerRegistryMock = $this->getMockBuilder('\Doctrine\Common\Persistence\ManagerRegistry')
            ->setMethods(array('getManagerForClass'))
            ->getMockForAbstractClass();

        $this->managerRegistryMock->expects($this->any())
            ->method('getManagerForClass')
            ->will($this->returnValue($this->managerMock));

        $this->objects = range('a', 'c');
        $this->objects[] = $this->testObject = new \stdClass();
    }

    public function testConstruct()
    {
        $doctrine = new Doctrine($this->managerRegistryMock, true);

        $this->assertInstanceOf('\h4cc\AliceFixturesBundle\ORM\ORMInterface', $doctrine);
    }

    public function testPersist()
    {
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('persist');
        $this->managerMock->expects($this->once())->method('flush');

        $doctrine = new Doctrine($this->managerRegistryMock, true);

        $doctrine->persist($this->objects);
    }

    public function testPersistWithoutFlush()
    {
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('persist');
        $this->managerMock->expects($this->never())->method('flush');

        $doctrine = new Doctrine($this->managerRegistryMock, false);

        $doctrine->persist($this->objects);
    }

    public function testMerge()
    {
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('merge')
            ->will($this->returnValueMap(array(
                array('a', 'a_merged'),
                array('b', 'b_merged'),
                array('c', 'c_merged'),
                array($this->testObject, 'testObject_merged'),
            )));
        $this->managerMock->expects($this->never())->method('flush');

        $doctrine = new Doctrine($this->managerRegistryMock, true);

        $mergedObjects = $doctrine->merge($this->objects);

        $this->assertEquals(array('a_merged', 'b_merged', 'c_merged', 'testObject_merged'), $mergedObjects);
    }

    public function testDetach()
    {
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('detach');
        $this->managerMock->expects($this->never())->method('flush');

        $doctrine = new Doctrine($this->managerRegistryMock, true);

        $doctrine->detach($this->objects);
    }

    public function testRemove()
    {
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('merge');
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('remove');
        $this->managerMock->expects($this->once())->method('flush');

        $doctrine = new Doctrine($this->managerRegistryMock, true);

        $doctrine->remove($this->objects);
    }

    public function testRemoveWithoutFlush()
    {
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('merge');
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('remove');
        $this->managerMock->expects($this->never())->method('flush');

        $doctrine = new Doctrine($this->managerRegistryMock, false);

        $doctrine->remove($this->objects);
    }

    public function testFind()
    {
        $this->managerMock->expects($this->exactly(count($this->objects)))->method('find')
            ->will($this->returnValueMap(array(
                array('Some/Class', 0, 'a'),
                array('Some/Class', 1, 'b'),
                array('Some/Class', 2, 'c'),
                array('Some/Class', 3, $this->testObject),
            )));
        $this->managerMock->expects($this->never())->method('flush');

        $doctrine = new Doctrine($this->managerRegistryMock, true);

        foreach($this->objects as $index => $expectedEntity) {
            $entity = $doctrine->find('Some/Class', $index);
            $this->assertEquals($expectedEntity, $entity);
        }
    }

    /**
     * @expectedException \UnexpectedValueException
     * @expectedExceptionMessage Entity with Id 42 and Class Some/Class not found
     */
    public function testFindNotFoundException()
    {
        $this->managerMock->expects($this->once())->method('find')
            ->will($this->returnValueMap(array(
                array('Some/Class', 42, null),
            )));
        $this->managerMock->expects($this->never())->method('flush');

        $doctrine = new Doctrine($this->managerRegistryMock, true);

        $doctrine->find('Some/Class', 42);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage No ObjectManager for class Some/Class
     */
    public function testNoObjectManager()
    {
        $this->managerRegistryMock = $this->getMockBuilder('\Doctrine\Common\Persistence\ManagerRegistry')
            ->setMethods(array('getManagerForClass'))
            ->getMockForAbstractClass();
        $this->managerRegistryMock->expects($this->once())->method('getManagerForClass')->with('Some/Class')->willReturn(null);

        $doctrine = new Doctrine($this->managerRegistryMock, true);

        $doctrine->find('Some/Class', 42);
    }
}
 