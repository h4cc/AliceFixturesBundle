<?php

$set = new \h4cc\AliceFixturesBundle\Fixtures\FixtureSet(
    array(
        'locale' => 'de_DE',
        'seed' => 42,
        'do_drop' => true,
        'do_persist' => true,
    )
);

return $set;
