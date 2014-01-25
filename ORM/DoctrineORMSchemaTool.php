<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\ORM;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool as DoctrineSchemaTool;

/**
 * Helper tool for creating and dropping ORM Schemas.
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class DoctrineORMSchemaTool implements SchemaToolInterface
{
    /**
     * @var DoctrineSchemaTool
     */
    protected $doctrineSchemaTool;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param EntityManager $entitiyManager
     */
    public function __construct(EntityManager $entitiyManager)
    {
        $this->entityManager = $entitiyManager;
        $this->doctrineSchemaTool = new DoctrineSchemaTool($entitiyManager);
    }

    /**
     * {@inheritDoc}
     */
    public function dropSchema()
    {
        $this->doctrineSchemaTool->dropDatabase();
    }

    /**
     * {@inheritDoc}
     */
    public function createSchema()
    {
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();

        $this->doctrineSchemaTool->createSchema($metadata);
    }
}
