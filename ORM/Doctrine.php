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

/**
 * Class Doctrine
 *
 * Adding more ORM actions.
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class Doctrine implements ORMInterface
{
    protected $flush;
    protected $managerRegistry;

    // We need to collect all the used managers for flushing them.
    protected $managersToFlush;

    public function __construct(ManagerRegistry $managerRegistry, $doFlush = true)
    {
        $this->flush = $doFlush;
        $this->managerRegistry = $managerRegistry;

        $this->managersToFlush = new \SplObjectStorage();
    }

    /**
     * {@inheritDoc}
     */
    public function persist(array $objects)
    {
        foreach ($objects as $object) {
            $manager = $this->getManagerFor($object);
            $this->managersToFlush->attach($manager);

            $manager->persist($object);
        }

        $this->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function find($class, $id)
    {
        $entity = $this->getManagerFor($class)->find($class, $id);

        if (!$entity) {
            throw new \UnexpectedValueException('Entity with Id ' . $id . ' and Class ' . $class . ' not found');
        }

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function remove(array $objects)
    {
        $objects = $this->merge($objects);

        foreach ($objects as $object) {
            $manager = $this->getManagerFor($object);
            $this->managersToFlush->attach($manager);

            $manager->remove($object);
        }

        $this->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function merge(array $objects)
    {
        $mergedObjects = array();

        foreach($objects as $object) {
            $mergedObjects[] = $this->getManagerFor($object)->merge($object);
        }

        return $mergedObjects;
    }

    /**
     * {@inheritDoc}
     */
    public function detach(array $objects)
    {
        foreach ($objects as $object) {
            $this->getManagerFor($object)->detach($object);
        }
    }

    private function getManagerFor($object)
    {
        if(is_object($object)) {
            $class = get_class($object);
        }else{
            $class = (string)$object;
        }

        $manager = $this->managerRegistry->getManagerForClass($class);

        if(!$manager) {
            throw new \RuntimeException('No ObjectManager for class '.$class);
        }

        return $manager;
    }

    private function flush()
    {
        if ($this->flush) {
            foreach($this->managersToFlush as $manager) {
                /** @var \Doctrine\Common\Persistence\ObjectManager $manager */
                $manager->flush();
            }
        }

        // Calling a static method in a not static way.
        $this->managersToFlush->removeAll($this->managersToFlush);
    }
}
 