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

use Doctrine\Common\Persistence\ObjectManager;
use Nelmio\Alice\ORM\Doctrine as BaseDoctrine;

/**
 * Class Doctrine
 *
 * Adding more ORM actions.
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class Doctrine extends BaseDoctrine
{
    protected $flush;

    /**
     * Wrapper to fetch the doFlush flag.
     *
     * @param ObjectManager $om
     * @param bool $doFlush
     */
    public function __construct(ObjectManager $om, $doFlush = true)
    {
        $this->flush = $doFlush;
        parent::__construct($om, $doFlush);
    }

    /**
     * Removes entities.
     *
     * @param array $objects
     */
    public function remove(array $objects)
    {
        $objects = $this->merge($objects);

        foreach ($objects as $object) {
            $this->om->remove($object);
        }

        if ($this->flush) {
            $this->om->flush();
        }
    }

    /**
     * Merges entities.
     *
     * @param array $objects
     *
     * @return array objects
     */
    public function merge(array $objects)
    {
        $om = $this->om;
        return array_map(
            function ($obj) use ($om) {
                return $om->merge($obj);
            },
            $objects
        );
    }

    /**
     * Detaches entities.
     *
     * @param array $objects
     */
    public function detach(array $objects)
    {
        foreach ($objects as $object) {
            $this->om->detach($object);
        }
    }
}
 