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

use Doctrine\ORM\Tools\SchemaTool as DoctrineSchemaTool;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class SchemaTool
 *
 * Helper tool for creating and dropping ORM Schemas.
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class SchemaTool implements SchemaToolInterface
{
    /**
     * @var DoctrineSchemaTool
     */
    protected $doctrineSchemaTool;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     * @param DoctrineSchemaTool $doctrineSchemaTool
     */
    public function __construct(ObjectManager $objectManager, DoctrineSchemaTool $doctrineSchemaTool)
    {
        $this->objectManager = $objectManager;
        $this->doctrineSchemaTool = $doctrineSchemaTool;
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
        $metadata = $this->objectManager->getMetadataFactory()->getAllMetadata();

        $this->doctrineSchemaTool->createSchema($metadata);
    }
}
