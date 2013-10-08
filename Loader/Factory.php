<?php


namespace h4cc\AliceFixturesBundle\Loader;

use Nelmio\Alice\Loader\Base as BaseLoader;
use Nelmio\Alice\Loader\Yaml as YamlLoader;

/**
 * Factory for loaders.
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
        switch($type) {
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
