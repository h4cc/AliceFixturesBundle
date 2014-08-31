<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests\DependencyInjection\Compiler;

use h4cc\AliceFixturesBundle\DependencyInjection\Compiler\ProcessorCompilerPass;

/**
 * Class ProcessorCompilerPassTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\DependencyInjection\Compiler\ProcessorCompilerPass
 */
class ProcessorCompilerPassTest extends \PHPUnit_Framework_TestCase
{
    /** @var  ProcessorCompilerPass */
    protected $compilerPass;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    protected $containerMock;

    protected function setUp()
    {
        $this->compilerPass = new ProcessorCompilerPass();

        $this->containerMock = $this->getMockBuilder('\Symfony\Component\DependencyInjection\ContainerBuilder')
          ->setMethods(array('findDefinition', 'findTaggedServiceIds'))
          ->disableOriginalConstructor()
          ->getMock();
    }

    public function testProcess()
    {
        $definitionMock = $this->getMockBuilder('\Symfony\Component\DependencyInjection\Definition')
          ->setMethods(array('addMethodCall'))
          ->disableOriginalConstructor()
          ->getMock();
        $this->containerMock->expects($this->once())->method('findDefinition')
          ->with('h4cc_alice_fixtures.manager')->will($this->returnValue($definitionMock));

        $taggedServices = array(
            'id1' => 'attributes1',
            'id2' => 'attributes2',
        );

        $this->containerMock->expects($this->once())->method('findTaggedServiceIds')
          ->with('h4cc_alice_fixtures.processor')->will($this->returnValue($taggedServices));

        $definitionMock->expects($this->exactly(2))->method('addMethodCall');

        $this->compilerPass->process($this->containerMock);
    }
}
