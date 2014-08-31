<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Tests\DependencyInjection;

use h4cc\AliceFixturesBundle\DependencyInjection\Configuration;
use Matthias\SymfonyConfigTest\PhpUnit\AbstractConfigurationTestCase;

/**
 * Class ConfiguratonTest
 *
 * @author Julius Beckmann <github@h4cc.de>
 * @covers h4cc\AliceFixturesBundle\DependencyInjection\Configuration
 */
class ConfigurationTest extends AbstractConfigurationTestCase
{
    protected function getConfiguration()
    {
        return new Configuration();
    }

    public function testDefaultConfiguration()
    {
        $this->assertProcessedConfigurationEquals(
            // Input
            array(),
            // Expected output
            array(
                'managers' => array(),
            )
        );
    }

    public function testSimpleConfiguration()
    {
        $this->assertProcessedConfigurationEquals(
            // Input
            array(array(
                'locale' => 'de_DE',
                'seed' => 9876,
                'do_flush' => false,

                'schema_tool' => 'doctrine_schema_tool',

                'doctrine' => 'mongodb-odm',

                'default_manager' => 'default',
            )),
            // Expected output
            array(
                'default_manager' => 'default',
                'managers' => array(
                    'default' => array(
                        'locale' => 'de_DE',
                        'seed' => 9876,
                        'do_flush' => false,

                        'schema_tool' => 'doctrine_schema_tool',

                        'doctrine' => 'mongodb-odm',
                    )
                ),
            )
        );
    }

    public function testMultiManagerConfiguration()
    {
        $this->assertProcessedConfigurationEquals(
            // Input
            array(array(
                'default_manager' => 'my_manager',
                'managers' => array(
                    'my_manager' => array(
                        'locale' => 'de_DE',
                        'seed' => 9876,
                        'do_flush' => false,

                        'schema_tool' => 'doctrine_schema_tool',

                        'doctrine' => 'mongodb-odm',
                    )
                )
            )),
            // Expected output
            array(
                'default_manager' => 'my_manager',
                'managers' => array(
                    'my_manager' => array(
                        'locale' => 'de_DE',
                        'seed' => 9876,
                        'do_flush' => false,

                        'schema_tool' => 'doctrine_schema_tool',

                        'doctrine' => 'mongodb-odm',
                    )
                ),
            )
        );
    }
}