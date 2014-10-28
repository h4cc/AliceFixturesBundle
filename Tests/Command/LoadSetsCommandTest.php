<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests\Command;

use h4cc\AliceFixturesBundle\Command\LoadSetsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LoadSetsCommandTest.
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\Command\LoadSetsCommand
 */
class LoadSetsCommandTest extends \PHPUnit_Framework_TestCase
{
    private $application;

    private $command;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $managerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $kernelMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $bundleMock;

    public function setUp()
    {
        $this->managerMock = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface')
          ->setMethods(array('load'))
          ->getMockForAbstractClass();

        $this->bundleMock = $this->getMockBuilder('\Symfony\Component\HttpKernel\Bundle\BundleInterface')
          ->setMethods(array('getPath'))
          ->getMockForAbstractClass();

        $this->kernelMock = $this->getMockBuilder('\Symfony\Component\HttpKernel\KernelInterface')
          ->setMethods(array('getBundles'))
          ->getMockForAbstractClass();
        $this->kernelMock->expects($this->any())->method('getBundles')->will($this->returnValue(array($this->bundleMock)));


        $this->application = new Application();
        $this->application->add(new LoadSetsCommand());

        $container = new Container();
        $container->set('h4cc_alice_fixtures.manager', $this->managerMock);
        $container->set('h4cc_alice_fixtures.mongodb_manager', $this->managerMock);
        $container->set('kernel', $this->kernelMock);

        $this->command = $this->application->find('h4cc_alice_fixtures:load:sets');
        $this->command->setContainer($container);
    }

    public function testLoad()
    {
        $this->managerMock->expects($this->once())->method('load')->willReturn(array('abc' => 42));

        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName(), 'sets' => array(__DIR__ . '/../testdata/SimpleSet.php'))
        );
    }

    public function testLoadWithoutDefaultManager()
    {
        $this->managerMock->expects($this->once())->method('load')->willReturn(array('abc' => 42));

        $tester = new CommandTester($this->command);

        $tester->execute(
            array(
                'command' => $this->command->getName(),
                '--manager' => 'mongodb',
                'sets' => array(__DIR__ . '/../testdata/SimpleSet.php'),
            )
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage File 'Tests/testdata/InvalidSet.php' does not return a FixtureSetInterface.
     */
    public function testLoadExceptionNotAFixtureSetInterface()
    {
        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName(), 'sets' => array('Tests/testdata/InvalidSet.php'))
        );
    }

    public function testLoadErrorNoSets()
    {
        $this->bundleMock->expects($this->any())->method('getPath')->will($this->returnValue('/bar'));

        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName())
        );

        $this->assertEquals("No sets to load\n", $tester->getDisplay());
    }

    public function testLoadWithDefaultLoadedSet()
    {
        $this->bundleMock->expects($this->any())->method('getPath')->will($this->returnValue(__DIR__.'/SampleBundle'));
        $this->managerMock->expects($this->once())->method('load')->willReturn(array('abc' => 42));

        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName())
        );

        $this->assertEquals("Loading file '".__DIR__.'/SampleBundle'."/DataFixtures/Alice/FooSet.php' ... loaded 1 entities ... done.\n", $tester->getDisplay());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage FixtureSet file does not exist: 'not_existing_file'.
     */
    public function testLoadExceptionFileNotExist()
    {
        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName(), 'sets' => array('not_existing_file'))
        );
    }
}
