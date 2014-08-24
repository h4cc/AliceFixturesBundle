<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class h4ccAliceFixturesExtension
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class h4ccAliceFixturesExtension extends Extension
{
    const FIXTURE_MANAGER_NAME_MODEL = 'h4cc_alice_fixtures.%s_manager';
    const SCHEMA_TOOL_NAME_MODEL     = 'h4cc_alice_fixtures.orm.%s_schema_tool';

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->loadManagersServices($config, $container);
    }

    private function loadManagersServices(array $config, ContainerBuilder $container)
    {
        // If there is no default_manager set, use the first configured.
        if (empty($config['default_manager'])) {
            $keys = array_keys($config['managers']);
            $config['default_manager'] = reset($keys);
        }

        // Process all configured managers.
        foreach($config['managers'] as $name => $currentManagerConfig) {

            $managerConfig = array(
                'locale' => $currentManagerConfig['locale'],
                'seed' => $currentManagerConfig['seed'],
                'do_flush' => $currentManagerConfig['do_flush'],
            );

            $schemaToolServiceId = $this->getSchemaToolServiceIdForCurrentConfig($currentManagerConfig, $container);

            $managerServiceDefinition = new Definition();
            $managerServiceDefinition->setClass('%h4cc_alice_fixtures.manager.class%');
            $managerServiceDefinition->setArguments(array(
                $managerConfig,
                new Reference($this->getCleanDoctrineConfigName($currentManagerConfig['doctrine'])),
                new Reference('h4cc_alice_fixtures.loader.factory'),
                new Reference($schemaToolServiceId)
            ));

            // set manager
            $container->setDefinition(sprintf(static::FIXTURE_MANAGER_NAME_MODEL, $name), $managerServiceDefinition);

            // set a alias schema tool service to ease find by manager name
            $container->setAlias(sprintf(static::SCHEMA_TOOL_NAME_MODEL, $name), $schemaToolServiceId);
        }

        // set default alias fixture manager
        $container->setAlias(
            'h4cc_alice_fixtures.manager',
            sprintf(static::FIXTURE_MANAGER_NAME_MODEL, $config['default_manager'])
        );

        // set default alias schema tool
        $defaultManagerConfig = $config['managers'][$config['default_manager']];
        $container->setAlias(
            'h4cc_alice_fixtures.orm.schema_tool',
            $this->getSchemaToolServiceIdForCurrentConfig($defaultManagerConfig, $container)
        );
    }

    /**
     * Will return the configured schema_tool service id,
     * or will define a default one lazy and return its id.
     *
     * @param array $currentManagerConfig
     * @param ContainerBuilder $container
     * @return string
     */
    private function getSchemaToolServiceIdForCurrentConfig(array $currentManagerConfig, ContainerBuilder $container)
    {
        // If there is a schema_tool configured, use it.
        if(!empty($currentManagerConfig['schema_tool'])) {
            return $currentManagerConfig['schema_tool'];
        }

        $serviceId = sprintf(
            'h4cc_alice_fixtures.orm.schema_tool.%s',
            $this->getCleanDoctrineConfigName($currentManagerConfig['doctrine'])
        );

        if (!$container->has($serviceId)) {
            $schemaToolDefinition = new Definition();
            $schemaToolDefinition->setClass($this->getSchemaToolClass($currentManagerConfig['doctrine']));
            $schemaToolDefinition->setArguments(array(
                new Reference($this->getCleanDoctrineConfigName($currentManagerConfig['doctrine']))
            ));
            $container->setDefinition($serviceId, $schemaToolDefinition);
        }

        return $serviceId;
    }

    /**
     * Will return the default service id for the schema tool of current doctrine subsystem.
     *
     * @param $doctrineConfigName
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getSchemaToolClass($doctrineConfigName)
    {
        switch($doctrineConfigName) {
            case Configuration::DOCTRINE_ORM:
                return '%h4cc_alice_fixtures.orm.schema_tool.doctrine.class%';
            case Configuration::DOCTRINE_MONGODB_ODM:
                return '%h4cc_alice_fixtures.orm.schema_tool.mongodb.class%';
            default:
                throw new \InvalidArgumentException('Unknown doctrine config key value: '.$doctrineConfigName);
        }
    }

    /**
     * Will return the default service ids for the doctrine subsystems.
     *
     * @param $doctrineConfigName
     * @return string
     * @throws \InvalidArgumentException
     */
    private function getCleanDoctrineConfigName($doctrineConfigName)
    {
        switch($doctrineConfigName) {
            case Configuration::DOCTRINE_ORM:
                return 'doctrine';
            case Configuration::DOCTRINE_MONGODB_ODM:
                return 'doctrine_mongodb';
            default:
                throw new \InvalidArgumentException('Unknown doctrine config key value: '.$doctrineConfigName);
        }
    }
}
