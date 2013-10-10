<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests\DependencyInjection\Command;

use h4cc\AliceFixturesBundle\Command\LoadSetsCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LoadSetsCommandTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class LoadSetsCommandTest extends \PHPUnit_Framework_TestCase
{
    private $application;

    private $command;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $managerMock;

    public function setUp()
    {
        $this->managerMock = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface')
                             ->setMethods(array('load'))
                             ->getMockForAbstractClass();

        $this->application = new Application();
        $this->application->add(new LoadSetsCommand());

        $container = new Container();
        $container->set('h4cc_alice_fixtures.manager', $this->managerMock);

        $this->command = $this->application->find('h4cc_alice_fixtures:load:sets');
        $this->command->setContainer($container);
    }

    public function testLoad()
    {
        $this->managerMock->expects($this->once())->method('load');

        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName(), 'sets' => array(__DIR__ . '/../../testdata/SimpleSet.php'))
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadExceptionNotAFixtureSetInterface()
    {
        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName(), 'sets' => array(__DIR__ . '/../../testdata/InvalidSet.php'))
        );
    }

    public function testLoadErrorNoSets()
    {
        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName())
        );

        $this->assertEquals("No sets to load\n", $tester->getDisplay());
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
