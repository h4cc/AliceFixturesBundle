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

use h4cc\AliceFixturesBundle\ORM\MongoDBODMSchemaTool;

/**
 * Class MongoDBODMSchemaToolTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\ORM\MongoDBODMSchemaTool
 */
class MongoDBODMSchemaToolTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $managerRegistryMock;

    public function setUp()
    {
        $this->managerRegistryMock = $this->getMockBuilder('\Doctrine\Common\Persistence\ManagerRegistry')
            ->setMethods(array('getManagers'))
            ->getMockForAbstractClass();

        $this->managerRegistryMock->expects($this->any())
            ->method('getManagers')
            ->will($this->returnValue(array()));
    }

    public function testConstruct()
    {
        $schemaTool = new MongoDBODMSchemaTool($this->managerRegistryMock);

        $this->assertInstanceOf('\h4cc\AliceFixturesBundle\ORM\SchemaToolInterface', $schemaTool);
    }

    public function testDropSchema()
    {
        $schemaTool = new MongoDBODMSchemaTool($this->managerRegistryMock);

        // Not testing any further here for now, because mocking for DoctrineSchemaTool needs some effort.
        $schemaTool->dropSchema();
    }

    public function testCreateSchema()
    {
        $schemaTool = new MongoDBODMSchemaTool($this->managerRegistryMock);

        // Not testing any further here for now, because mocking for DoctrineSchemaTool needs some effort.
        $schemaTool->createSchema();
    }
}
 