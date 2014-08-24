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

use Nelmio\Alice\ORMInterface as NelmioORMInterface;

/**
 * Interface for Object Relation Mapping operations.
 */
interface ORMInterface extends NelmioORMInterface
{
    /**
     * Removes entities.
     *
     * @param array $objects
     */
    public function remove(array $objects);

    /**
     * Merges entities.
     *
     * @param array $objects
     *
     * @return array objects
     */
    public function merge(array $objects);

    /**
     * Detaches entities.
     *
     * @param array $objects
     */
    public function detach(array $objects);
}
