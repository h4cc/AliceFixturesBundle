<?php

namespace h4cc\AliceFixturesBundle\ORM;

use Doctrine\ORM\Tools\SchemaTool as DoctrineSchemaTool;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Helper tool for creating and dropping ORM Schemas.
 */
class SchemaTool implements SchemaToolInterface
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Replaces current Object Manager.
     *
     * @param ObjectManager $objectManager
     */
    public function setObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * {@inheritDoc}
     */
    public function dropSchema()
    {
        $this->getORMSchemaTool()->dropDatabase();
    }

    /**
     * {@inheritDoc}
     */
    public function createSchema()
    {
        $metadata = $this->objectManager->getMetadataFactory()->getAllMetadata();

        $this->getORMSchemaTool()->createSchema($metadata);
    }

    protected function getORMSchemaTool()
    {
        return new DoctrineSchemaTool($this->objectManager);
    }
}
