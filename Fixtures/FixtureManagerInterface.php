<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Fixtures;

/**
 * Interface FixtureManagerInterface
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
interface FixtureManagerInterface
{
    /**
     * Loads objects/entities from given files.
     * Does _not_ persist them, use persist() for that.
     *
     * @param array $files
     * @param string $type
     * @return array
     */
    public function loadFiles(array $files, $type = 'yaml');

    /**
     * Creates a new configured fixture set.
     *
     * @return \h4cc\AliceFixturesBundle\Fixtures\FixtureSet
     */
    public function createFixtureSet();

    /**
     * Loads objects/entities from given FixtureSet.
     * The FixtureSet Object will decide, if drop or persist will be done as well as all other parameters.
     *
     * The initial references array can be used to provide already loaded entities so they can be referenced.
     *
     * @param FixtureSet $set
     * @param array $initialReferences
     * @return mixed
     */
    public function load(FixtureSet $set, array $initialReferences = array());

    /**
     * Persists all given entities.
     * Set drop to true for recreating the whole ORM schema before loading.
     *
     * @param array $entities
     * @param bool $drop
     * @return mixed
     */
    public function persist(array $entities, $drop = false);

    /**
     * Remove all given entities.
     *
     * @param array $entities
     */
    public function remove(array $entities);
}
