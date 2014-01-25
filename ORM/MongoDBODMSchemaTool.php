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

use Doctrine\ODM\MongoDB\DocumentManager;

class MongoDBODMSchemaTool implements SchemaToolInterface
{
    protected $schemaManager;

    public function __construct(DocumentManager $documentManager)
    {
        $this->schemaManager = $documentManager->getSchemaManager();
    }

    /**
     * {@inheritDoc}
     */
    public function dropSchema()
    {
        $this->schemaManager->deleteIndexes();
        $this->schemaManager->dropCollections();

        // NOT Dropping Databases, because of potential permission problems.
        // (After dropping your own database, only a admin can recreate it.)
        //$this->schemaManager->dropDatabases();
    }

    /**
     * {@inheritDoc}
     */
    public function createSchema()
    {
        // We assume, that the database already exists and we have permissions for it.
        $this->schemaManager->createCollections();
        $this->schemaManager->ensureIndexes();
    }
}
