<?php

namespace h4cc\AliceFixturesBundle\ORM;

/**
 * Schema tool with basic schema operations.
 */
interface SchemaToolInterface
{
    /**
     * Removes current schema from database.
     */
    public function dropSchema();

    /**
     * Creates current schema in database.
     */
    public function createSchema();
}
