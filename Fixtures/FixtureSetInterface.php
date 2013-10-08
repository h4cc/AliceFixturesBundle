<?php

namespace h4cc\AliceFixturesBundle\Fixtures;

/**
 * Set of files and options for import with FixtureManager.
 */
interface FixtureSetInterface
{
    /**
     * Adds a file to the set.
     *
     * @param $path
     * @param $type
     */
    public function addFile($path, $type);

    /**
     * Returns a list of file paths and types.
     *
     * @return array
     */
    public function getFiles();

    /**
     * @return boolean
     */
    public function getDoDrop();

    /**
     * @return boolean
     */
    public function getDoPersist();

    /**
     * @return string
     */
    public function getLocale();

    /**
     * @return int|null
     */
    public function getSeed();
}
