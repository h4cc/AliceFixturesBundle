<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Loader;

use Nelmio\Alice\Loader\Base as BaseLoader;
use Nelmio\Alice\Loader\Yaml as YamlLoader;

/**
 * Class Factory
 * Factory for loaders.
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class Factory implements FactoryInterface
{
    /**
     * Returns a loader for a specific type and locale.
     *
     * @param $type
     * @param $locale
     * @return BaseLoader|YamlLoader
     * @throws \InvalidArgumentException
     */
    public function getLoader($type, $locale)
    {
        switch ($type) {
            case 'yaml':
                return $this->newLoaderYaml($locale);
            case 'php':
                return $this->newLoaderPHP($locale);
        }
        throw new \InvalidArgumentException("Unknown loader type '$type'.");
    }

    protected function newLoaderYaml($locale)
    {
        return new YamlLoader($locale);
    }

    protected function newLoaderPHP($locale)
    {
        return new BaseLoader($locale);
    }
}
