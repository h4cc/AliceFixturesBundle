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

use h4cc\AliceFixturesBundle\Command\LoadFilesCommand;
use h4cc\AliceFixturesBundle\Fixtures\FixtureSet;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class LoadFilesCommandTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\Command\LoadFilesCommand
 */
class LoadFilesCommandTest extends \PHPUnit_Framework_TestCase
{
    private $application;

    private $command;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $managerMock;

    /** @var  \PHPUnit_Framework_MockObject_MockObject */
    private $schemaToolMock;

    public function setUp()
    {
        $this->managerMock = $this->getMockBuilder('\h4cc\AliceFixturesBundle\Fixtures\FixtureManagerInterface')
          ->setMethods(array('load', 'createFixtureSet'))
          ->getMockForAbstractClass();

        $this->managerMock->expects($this->any())->method('createFixtureSet')->will(
            $this->returnValue(new FixtureSet())
        );

        $this->schemaToolMock = $this->getMockBuilder('\h4cc\AliceFixturesBundle\ORM\SchemaToolInterface')
          ->setMethods(array('dropSchema', 'createSchema'))
          ->getMockForAbstractClass();

        $this->application = new Application();
        $this->application->add(new LoadFilesCommand());

        $container = new Container();
        $container->set('h4cc_alice_fixtures.manager', $this->managerMock);
        $container->set('h4cc_alice_fixtures.orm.schema_tool', $this->schemaToolMock);

        $container->set('h4cc_alice_fixtures.mongodb_manager', $this->managerMock);
        $container->set('h4cc_alice_fixtures.orm.mongodb_schema_tool', $this->schemaToolMock);

        $this->command = $this->application->find('h4cc_alice_fixtures:load:files');
        $this->command->setContainer($container);
    }

    public function testLoad()
    {
        $this->managerMock->expects($this->once())->method('load')->willReturn(array('abc' => 42));

        $tester = new CommandTester($this->command);

        $tester->execute(
            array(
                'command' => $this->command->getName(),
                'files' => array(__DIR__ . '/../testdata/part_1.yml'),
                '--drop' => true
            )
        );
    }

    public function testLoadWithoutDefaultManager()
    {
        $this->managerMock->expects($this->once())->method('load')->willReturn(array('abc' => 42));

        $tester = new CommandTester($this->command);

        $tester->execute(
            array(
                'command' => $this->command->getName(),
                'files' => array(__DIR__ . '/../testdata/part_1.yml'),
                '--manager' => 'mongodb',
                '--drop' => true
            )
        );
    }

    public function testLoadErrorNoFiles()
    {
        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName())
        );

        $this->assertEquals("No files to load\n", $tester->getDisplay());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Fixture file does not exist: 'not_existing_file'.
     */
    public function testLoadExceptionFileNotExist()
    {
        $tester = new CommandTester($this->command);

        $tester->execute(
            array('command' => $this->command->getName(), 'files' => array('not_existing_file'))
        );
    }
}
