<?php

/*
 * This file is part of the h4cc/AliceDemo package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// This set is intended to do nothing.
return new h4cc\AliceFixturesBundle\Fixtures\FixtureSet(array(
    'locale' => 'de_DE',
    'seed' => 42,
    'do_drop' => true,
    'do_persist' => true,
));
