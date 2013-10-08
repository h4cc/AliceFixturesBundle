<?php

namespace h4cc\AliceFixturesBundle\Fixtures;

use h4cc\AliceFixturesBundle\ORM\SchemaTool;
use h4cc\AliceFixturesBundle\ORM\SchemaToolInterface;
use Nelmio\Alice\Fixtures;
use Nelmio\Alice\Loader\Base;
use Nelmio\Alice\LoaderInterface;
use Nelmio\Alice\ORM\Doctrine;
use Nelmio\Alice\ORMInterface;
use Nelmio\Alice\ProcessorInterface;
use Psr\Log\LoggerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use h4cc\AliceFixturesBundle\Loader\Factory;
use h4cc\AliceFixturesBundle\Loader\FactoryInterface;

class FixtureManager implements FixtureManagerInterface
{
    /**
     * Global provider for Faker.
     *
     * @var array
     */
    protected $providers = array();

    /**
     * @var ProcessorInterface[]
     */
    protected $processors = array();

    /**
     * Optional logger.
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Default options for new FixtureSets.
     *
     * @var array
     */
    protected $options = array();

    /**
     * @var \Nelmio\Alice\ORM\Doctrine
     */
    protected $orm;

    /**
     * @var SchemaToolInterface
     */
    protected $schemaTool;

    /**
     * @param array $options
     * @param ObjectManager $objectManager
     * @param \h4cc\AliceFixturesBundle\Loader\Factory|\h4cc\AliceFixturesBundle\Loader\FactoryInterface $loaderFactory
     * @param \h4cc\AliceFixturesBundle\ORM\SchemaToolInterface $schemaTool
     * @param LoggerInterface $logger
     */
    public function __construct(
        array $options = array(),
        ObjectManager $objectManager,
        FactoryInterface $loaderFactory,
        SchemaToolInterface $schemaTool,
        LoggerInterface $logger = null
    ) {
        $this->options = array_merge(
            $this->getDefaultOptions(),
            $options
        );
        $this->orm = new Doctrine($objectManager, $this->options['do_flush']);
        $this->loaderFactory = $loaderFactory;
        $this->schemaTool = $schemaTool;
        $this->logger = $logger;
    }

    /**
     * Returns a new configured fixture set.
     *
     * @return FixtureSet
     */
    public function createFixtureSet()
    {
        return new FixtureSet($this->options);
    }

    /**
     * Returns a new instance of a ORM Schema tool.
     *
     * @throws \InvalidArgumentException
     * @return SchemaTool
     */
    public function getSchemaTool()
    {
        return $this->schemaTool;
    }

    /**
     * Returns a new instance of the ORM.
     *
     * @throws \InvalidArgumentException
     * @return Doctrine
     */
    public function getORM()
    {
        return $this->orm;
    }

    /**
     * {@inheritDoc}
     */
    public function load(FixtureSet $set)
    {
        /** @var \Nelmio\Alice\LoaderInterface[] $loaders */
        $loaders = array();

        // Create needed loaders
        foreach ($set->getFiles() as $file) {
            $type = $file['type'];
            if (!isset($loaders[$type])) {
                $loader = $this->loaderFactory->getLoader($type, $set->getLocale());
                $this->configureLoaderFromSet($loader, $set);
                $loaders[$type] = $loader;
                $this->logDebug("Created loader for type '$type'.");
            }
        }

        // Load files
        $references = array();
        $objects = array();
        foreach ($set->getFiles() as $file) {
            // Use seed before each loading, so results will be more predictable.
            $this->initSeedFromSet($set);

            $loader = $loaders[$file['type']];
            $loader->setReferences($references);
            $objects = array_merge($objects, $loader->load($file['path']));
            $references = $loader->getReferences();
            $this->logDebug("Loaded file '".$file['path']."'.");
        }

        if ($set->getDoPersist()) {
            $this->persist($objects, $set->getDoDrop());
            $this->logDebug("Persisted ".count($objects)." loaded objects.");
        }

        return $objects;
    }

    /**
     * @param array $files
     * @param string $type
     * @return array
     */
    public function loadFiles(array $files, $type = 'yaml')
    {
        $set = $this->createFixtureSet();
        foreach ($files as $file) {
            $set->addFile($file, $type);
        }
        return $this->load($set);
    }

    /**
     * Persists all given entities.
     *
     * @param array $entities
     * @param bool $drop
     * @return mixed
     */
    public function persist(array $entities, $drop = false)
    {
        if ($drop) {
            $this->recreateSchema();
        }
        $this->persistObjects($this->getORM(), $entities);
    }

    /**
     * Returns global options.
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @return array
     */
    public function getDefaultOptions()
    {
        return array(
            'seed' => 1,
            'locale' => 'en_EN',
            'do_flush' => true,
        );
    }

    /**
     * Adds a processor for processing a entitiy before and after persisting.
     *
     * @param ProcessorInterface $processor
     */
    public function addProcessor(ProcessorInterface $processor)
    {
        $this->processors[] = $processor;
        $this->logDebug('Added processor: '.get_class($processor));
    }

    /**
     * Adds a provider for Faker.
     *
     * @param $provider
     */
    public function addProvider($provider)
    {
        $this->providers[] = $provider;
        $this->providers = array_unique($this->providers);
        $this->logDebug('Added provider: '.get_class($provider));
    }

    /**
     * Sets a list of providers for Faker.
     *
     * @param array $providers
     */
    public function setProviders(array $providers)
    {
        $this->providers = array();
        foreach($providers as $provider) {
            $this->addProvider($provider);
        }
    }

    /**
     * Will drop and create the current ORM Schema.
     */
    protected function recreateSchema()
    {
        $schemaTool = $this->getSchemaTool();
        $schemaTool->dropSchema();
        $schemaTool->createSchema();
        $this->logDebug('Recreated Schema');
    }

    /**
     * Initializes the seed for random numbers, given by a fixture set.
     *
     * @param FixtureSet $set
     */
    protected function initSeedFromSet(FixtureSet $set)
    {
        if (is_numeric($set->getSeed())) {
            mt_srand($set->getSeed());
            $this->logDebug('Initialized with seed '.$set->getSeed());
        } else {
            mt_srand();
            $this->logDebug('Initialized with random seed');
        }
    }

    /**
     * Sets all needed options and dependencies to a loader given by a fixture set.
     *
     * @param LoaderInterface $loader
     * @param FixtureSet $set
     */
    protected function configureLoaderFromSet(LoaderInterface $loader, FixtureSet $set)
    {
        if ($loader instanceof Base) {
            $loader->setORM($this->getORM());
            if ($this->logger) {
                $loader->setLogger($this->logger);
            }
        }
        $loader->setProviders($this->providers);
    }

    /**
     * Persists given objects using ORM persister, and calls registered processors.
     *
     * @param ORMInterface $persister
     * @param $objects
     */
    protected function persistObjects(ORMInterface $persister, array $objects)
    {
        foreach ($this->processors as $proc) {
            foreach ($objects as $obj) {
                $proc->preProcess($obj);
            }
        }

        $persister->persist($objects);

        foreach ($this->processors as $proc) {
            foreach ($objects as $obj) {
                $proc->postProcess($obj);
            }
        }
    }

    /**
     * Logs a message in debug level.
     *
     * @param $message
     */
    protected function logDebug($message)
    {
        if ($this->logger) {
            $this->logger->debug($message);
        }
    }
}
