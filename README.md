AliceFixturesBundle
===================

A Symfony2 bundle for flexible usage of Alice and Faker in symfony2.

## Introduction

The aim of this bundle is to provide a new way of working with data fixtures detached from the common Doctrine DataFixtures.
Loading of fixtures should be decoupled and easy to integrate where needed.
This bundle offers loading Fixtures from yaml and php files, also dropping recreating the ORM Schema.

If you are searching for Bundle, that provides a way to integrate Alice with Doctrine DataFixtures, have a look at [https://github.com/hautelook/AliceBundle].

## Installation

TODO: Register at Packagist and write composer installation howto.


## Configuration

You can configure the Seed für random values, the Locale for Faker and a global flag do_flush, if ORM flushes should be omitted.

```yaml
# app/config/config.yml

h4cc_alice_fixtures:
    locale: en_US   # default
    seed: 1         # default
    do_flush: false # default
```

## Usage

TODO: Write some usage examples.


## Future and ToDos:

- Unit and functional tests