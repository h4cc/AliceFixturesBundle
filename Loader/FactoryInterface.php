<?php

namespace h4cc\AliceFixturesBundle\Loader;

use Nelmio\Alice\LoaderInterface;

/**
 * Factory for loaders.
 */
interface FactoryInterface
{
    /**
     * Returns a loader for a specific type and locale.
     *
     * @param $type
     * @param $locale
     * @return LoaderInterface
     */
    public function getLoader($type, $locale);
}
