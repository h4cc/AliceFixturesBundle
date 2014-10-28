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
 * Class FixtureSetInterface
 * Set of files and options for import with FixtureManager.
 *
 * @author Julius Beckmann <github@h4cc.de>
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

    /**
     * @return int
     */
    public function getOrder();
}
