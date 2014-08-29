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

use h4cc\AliceFixturesBundle\Fixtures\FixtureManager;
use Nelmio\Alice\Loader\Yaml;

/**
 * Class FixtureManagerTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\Fixtures\FixtureManager
 */
class FixtureManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var \h4cc\AliceFixturesBundle\Fixtures\FixtureManager */
    protected $manager;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $objectManagerMock;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $managerRegistryMock;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $schemaToolMock;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $yamlLoaderMock;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $factoryMock;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $loggerMock;
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $processorMock;

    public function setUp()
    {
        $this->objectManagerMock = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
          ->disableOriginalConstructor()
          ->getMock();

        $this->managerRegistryMock = $this->getMockBuilder('\Doctrine\Common\Persistence\ManagerRegistry')
          ->setMethods(array('getManagerForClass'))
          ->disableOriginalConstructor()
          ->getMockForAbstractClass();

        $this->schemaToolMock = $this->getMockBuilder('\h4cc\AliceFixturesBundle\ORM\SchemaToolInterface')
          ->disableOriginalConstructor()
          ->getMock();

        $this->yamlLoaderMock = $this->getMockBuilder('\Nelmio\Alice\Loader\Yaml')
          ->setMethods(array('setProviders', 'load'))
          ->disableOriginalConstructor()
          ->getMock();

        $this->factoryMock = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Loader\FactoryInterface')
          ->setMethods(array('getLoader'))
          ->getMockForAbstractClass();

        $this->loggerMock = $this->getMockBuilder('\Psr\Log\LoggerInterface')
          ->getMockForAbstractClass();

        $this->manager = new FixtureManager(
            array(),
            $this->managerRegistryMock,
            $this->factoryMock,
            $this->schemaToolMock,
            $this->loggerMock
        );

        $this->processorMock = $this->getMockBuilder('\Nelmio\Alice\ProcessorInterface')
          ->setMethods(array('preProcess', 'postProcess'))
          ->getMockForAbstractClass();
    }

    public function testDefaults()
    {
        $this->assertEquals(
            array('seed' => 1, 'locale' => 'en_EN', 'do_flush' => true),
            $this->manager->getOptions()
        );
        $this->assertEquals(
            array('seed' => 1, 'locale' => 'en_EN', 'do_flush' => true),
            $this->manager->getDefaultOptions()
        );
    }

    /**
     * Test that a created fixture set has correct default values.
     */
    public function testCreateFixtureSet()
    {
        $set = $this->manager->createFixtureSet();
        $this->assertEquals(
            array(
                false,
                true,
                array(),
                'en_EN',
                1
            ),
            array(
                $set->getDoDrop(),
                $set->getDoPersist(),
                $set->getFiles(),
                $set->getLocale(),
                $set->getSeed()
            )
        );
    }

    /**
     * Nothing will happen with no files.
     */
    public function testLoadNoFiles()
    {
        $set = $this->manager->createFixtureSet();
        $set->setDoPersist(false);
        $entities = $this->manager->load($set);
        $this->assertEquals(array(), $entities);

        $entities = $this->manager->loadFiles(array());
        $this->assertEquals(array(), $entities);
    }

    public function testLoadYaml()
    {
        $this->factoryMock->expects($this->any())->method('getLoader')
          ->with('yaml', 'en_EN')->will($this->returnValue(new Yaml()));

        $this->managerRegistryMock->expects($this->any())->method('getManagerForClass')
          ->will($this->returnValue($this->objectManagerMock));

        $entities = $this->manager->loadFiles(array(__DIR__ . '/../testdata/part_1.yml'));

        $this->assertCount(11, $entities);
        $this->assertInstanceOf('\h4cc\AliceFixturesBundle\Tests\testdata\User', $entities['user0']);
    }

    public function testProcessor()
    {
        $this->factoryMock->expects($this->any())->method('getLoader')
          ->with('yaml', 'en_EN')->will($this->returnValue(new Yaml()));

        $this->managerRegistryMock->expects($this->any())->method('getManagerForClass')
          ->will($this->returnValue($this->objectManagerMock));

        $this->processorMock->expects($this->exactly(11))->method('preProcess');
        $this->processorMock->expects($this->exactly(11))->method('postProcess');

        $this->manager->addProcessor($this->processorMock);

        $set = $this->manager->createFixtureSet();
        $set->setSeed(null);
        $set->addFile(__DIR__ . '/../testdata/part_1.yml', 'yaml');

        $this->manager->load($set);
    }

    public function testPersistAndDrop()
    {
        $this->schemaToolMock->expects($this->once())->method('dropSchema');
        $this->schemaToolMock->expects($this->once())->method('createSchema');

        $this->manager->persist(array(), true);
    }

    public function testRemove()
    {
        $this->managerRegistryMock->expects($this->any())->method('getManagerForClass')
          ->will($this->returnValue($this->objectManagerMock));

        $this->objectManagerMock->expects($this->once())->method('merge')->with('42')->will($this->returnValue('1337'));
        $this->objectManagerMock->expects($this->once())->method('remove')->with('1337');

        $this->manager->remove(array('42'));
    }

    public function testProviders()
    {
        $this->factoryMock->expects($this->any())->method('getLoader')
          ->with('yaml', 'en_EN')->will($this->returnValue($this->yamlLoaderMock));

        $this->yamlLoaderMock->expects($this->once())->method('load')->will($this->returnValue(array()));
        $this->yamlLoaderMock->expects($this->once())->method('setProviders');

        $provider = function () {
            return "foobar";
        };

        $this->manager->addProvider($provider);
        $this->manager->setProviders(array($provider));

        $this->manager->loadFiles(array(__DIR__ . '/../testdata/part_1.yml'));
    }

    /**
     * Issue 11 https://github.com/h4cc/AliceFixturesBundle/issues/11
     * Load objects, but do not return the "local" ones, because these can not be persisted by doctrine.
     */
    public function testLocalEntities()
    {
        // We need a real YAML Loader for this.
        $this->factoryMock->expects($this->any())->method('getLoader')
          ->with('yaml', 'en_EN')->will($this->returnValue(new Yaml()));

        $this->managerRegistryMock->expects($this->any())->method('getManagerForClass')
          ->will($this->returnValue($this->objectManagerMock));

        $objects = $this->manager->loadFiles(array(__DIR__ . '/../testdata/local_date.yml'));

        // The result should only contain the "group", not the "date".
        $this->assertEquals(array('group'), array_keys($objects));
        $this->assertEquals("2000-01-01 00:00:00", $objects['group']->getCreationDate()->format('Y-m-d H:i:s'));
    }
}
