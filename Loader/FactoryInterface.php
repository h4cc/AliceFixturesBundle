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

use Nelmio\Alice\Fixtures\Loader;

/**
 * Interface FactoryInterface
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
interface FactoryInterface
{
    /**
     * Returns a loader for a specific type and locale.
     *
     * @param $locale
     * @return Loader
     */
    public function getLoader($locale);
}
