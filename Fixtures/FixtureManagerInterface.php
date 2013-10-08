<?php

namespace h4cc\AliceFixturesBundle\Fixtures;

interface FixtureManagerInterface
{
    /**
     * Loads objects/entities from given files.
     *
     * @param array $files
     * @param string $type
     * @return array
     */
    public function loadFiles(array $files, $type='yaml');

    /**
     * Creates a new configured fixture set.
     *
     * @return \h4cc\AliceFixturesBundle\Fixtures\FixtureSet
     */
    public function createFixtureSet();

    /**
     * Loads objects/entites from given FixtureSet.
     *
     * @param FixtureSet $set
     * @return mixed
     */
    public function load(FixtureSet $set);

    /**
     * Persists all given entities.
     * Set drop to true for recreating the whole ORM schema before loading.
     *
     * @param array $entities
     * @param bool $drop
     * @return mixed
     */
    public function persist(array $entities, $drop=false);
}
