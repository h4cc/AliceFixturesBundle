<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests\ORM;

use h4cc\AliceFixturesBundle\ORM\SchemaTool;

/**
 * Class SchemaToolTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class SchemaToolTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $omMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $doctrineSMMock;

    /**
     * @var SchemaTool
     */
    protected $schema;

    protected function setUp()
    {
        $this->omMock = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
          ->setMethods(array('getMetadataFactory'))
          ->getMockForAbstractClass();

        $this->doctrineSMMock = $this->getMockBuilder('\Doctrine\ORM\Tools\SchemaTool')
          ->setMethods(array('dropDatabase', 'createSchema'))
          ->disableOriginalConstructor()
          ->getMock();

        $this->schema = new SchemaTool($this->omMock, $this->doctrineSMMock);
    }

    public function testDropSchema()
    {
        $this->doctrineSMMock->expects($this->once())->method('dropDatabase');

        $this->schema->dropSchema();
    }

    public function testCreateSchema()
    {
        $metadataMock = $this->getMockBuilder('\Doctrine\Common\Persistence\Mapping\ClassMetadataFactory')
          ->setMethods(array('getAllMetadata'))
          ->getMockForAbstractClass();
        $metadataMock->expects($this->once())->method('getAllMetadata')->will($this->returnValue(array(42)));

        $this->omMock->expects($this->once())->method('getMetadataFactory')->will($this->returnValue($metadataMock));

        $this->doctrineSMMock->expects($this->once())->method('createSchema')->with(array(42));

        $this->schema->createSchema();
    }
}
