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

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ODM\MongoDB\DocumentManager;

class MongoDBODMSchemaTool implements SchemaToolInterface
{
    protected $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * {@inheritDoc}
     */
    public function dropSchema()
    {
        $this->foreachObjectManagers(function(DocumentManager $objectManager) {
            $schemaManager = $objectManager->getSchemaManager();
            $schemaManager->deleteIndexes();
            $schemaManager->dropCollections();

            // NOT Dropping Databases, because of potential permission problems.
            // (After dropping your own database, only a admin can recreate it.)
            //$schemaManager->dropDatabases();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function createSchema()
    {
        $this->foreachObjectManagers(function(DocumentManager $objectManager) {
            $schemaManager = $objectManager->getSchemaManager();

            // We assume, that the database already exists and we have permissions for it.
            $schemaManager->createCollections();
            $schemaManager->ensureIndexes();
        });
    }

    private function foreachObjectManagers($callback)
    {
        array_map($callback, $this->managerRegistry->getManagers());
    }
}
