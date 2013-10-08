<?php

namespace h4cc\AliceFixturesBundle\Fixtures;

/**
 * Set of files and options for import with FixtureManager.
 */
class FixtureSet implements FixtureSetInterface
{
    protected $files = array();

    protected $options;

    public function __construct(array $options = array())
    {
        $defaultOptions = array(
            'locale' => 'en_US',
            'seed' => 1,
            'do_drop' => false,
            'do_persist' => true,
        );
        $this->options = array_merge(
            $defaultOptions,
            $options
        );
    }

    /**
     * Adds a file to the set.
     *
     * @param $path
     * @param $type
     */
    public function addFile($path, $type)
    {
        $this->files[md5($path)] = array(
            'type' => $type,
            'path' => $path,
        );
    }

    /**
     * Returns a list of file paths and types.
     *
     * @return array
     */
    public function getFiles()
    {
        return array_values($this->files);
    }

    /**
     * @return boolean
     */
    public function getDoDrop()
    {
        return $this->options['do_drop'];
    }

    /**
     * @param boolean $doDrop
     */
    public function setDoDrop($doDrop)
    {
        $this->options['do_drop'] = (boolean)$doDrop;
    }

    /**
     * @return boolean
     */
    public function getDoPersist()
    {
        return $this->options['do_persist'];
    }

    /**
     * @param boolean $doPersist
     */
    public function setDoPersist($doPersist)
    {
        $this->options['do_persist'] = (boolean)$doPersist;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->options['locale'];
    }

    /**
     * @param string $locale
     */
    public function setLocale($locale)
    {
        $this->options['locale'] = $locale;
    }

    /**
     * @return int|null
     */
    public function getSeed()
    {
        return $this->options['seed'];
    }

    /**
     * @param int $seed
     */
    public function setSeed($seed)
    {
        $this->options['seed'] = is_null($seed) ? null : (integer)$seed;
    }
}
