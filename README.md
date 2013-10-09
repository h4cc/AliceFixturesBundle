AliceFixturesBundle
===================

A Symfony2 bundle for flexible usage of Alice and Faker in symfony2.

## Introduction

The aim of this bundle is to provide a new way of working with data fixtures detached from the common Doctrine DataFixtures.
Loading of fixtures should be decoupled and easy to integrate where needed.
This bundle offers loading Fixtures from yaml and php files, also dropping recreating the ORM Schema.

If you are searching for Bundle, that provides a way to integrate Alice with Doctrine DataFixtures, have a look at [hautelook/AliceBundle](https://github.com/hautelook/AliceBundle).

This bundle is also capeable of recreating the ORM schema.
This means _all_ tables managed by Doctrine will be dropped and recreated. A data loss might appear, you have been warned.


## Installation

Simply require the bundle by its name with composer:
```bash
$ php composer.phar require h4cc/alice-fixtures-bundle
```
Follow the 'dev-master' branch for latest dev version. But i recommend to use more stable version tags if available.


After that, add the Bundle to your Kernel:
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new h4cc\AliceFixturesBundle\h4ccAliceFixturesBundle(),
        // ...
    );
}
```


## Configuration

You can globaly configure the Seed für random values, the Locale for Faker and a global flag do_flush, if ORM flushes of entities should be omitted or not.

```yaml
# app/config/config.yml

h4cc_alice_fixtures:
    locale: en_US   # default
    seed: 1         # default
    do_flush: false # default
```

## Usage

### Fixtures

Fixtures are defined in YAML or PHP Files like [nelmio/alice](https://github.com/nelmio/alice) describes it.
The two alice default loaders 'Yaml' and 'Base' are already available.
If you want to integrate own loaders, patch the Loader Factory service for your needs ('h4cc_alice_fixtures.loader.factory').

Loading the entities from single fixture files can be done with the FixtureManagerInterface::loadFiles(array $files, $type='yaml');.
Persisting them can be done with FixtureManagerInterface::persist(array $entities, $drop=false);.

```php
$files = array(__DIR__.'/fixtures/Users.yml', __DIR__.'/fixtures/Articles.yml');

$manager = $this->get('h4cc_alice_fixtures.manager');
$objects = $manager->loadFiles($files, 'yaml');
// Manipulate or add own objects here ...
$manager->persist($objects, true);
```

### Fixture Sets

A more advanced way of loading is fixtures is creating "FixtureSets".
Look at it like a Fixture configuration object for multiple fixture files and options.

```php
$manager = $this->get('h4cc_alice_fixtures.manager');

$set = $manager->createFixtureSet();
$set->addFile(__DIR__.'/fixtures/Users.yml', 'yaml');
$set->addFile(__DIR__.'/fixtures/Articles.yml', 'yaml');

// Change locale for this set only.
$set->setLocale('de_DE');
// Define a custom random seed for "predictable randomness".
$set->setSeed(42);
// Enable persisting of objects
$set->setDoPersist(true);
// Enable dropping and recreating current ORM schema.
$set->setDoDrop(true);

$manager->load($set);
```

### Commands

Like with Doctrine DataFixtures, there are some command for loading fixtures included in this bundle.
They are also divided in loading plain files or FixtureSets.

```
h4cc_alice_fixtures
  h4cc_alice_fixtures:load:files        Load fixture files using alice and faker.
  h4cc_alice_fixtures:load:sets         Load fixture sets using alice and faker.
```

Loading single files can be done with this command:
```bash
$ php app/console h4cc_alice_fixtures:load:files -h
Usage:
 h4cc_alice_fixtures:load:files [-t|--type[="..."]] [--seed[="..."]] [-l|--locale[="..."]] [-p|--persist[="..."]] [-d|--drop[="..."]] [files1] ... [filesN]

Arguments:
 files                 List of files to import.

Options:
 --type (-t)           Type of loader. Can be "yaml" or "php". (default: "yaml")
 --seed                Seed for random generator. (default: 1)
 --locale (-l)         Locale for Faker provider. (default: "en_EN")
 --persist (-p)        Persist loaded entities in database. (default: true)
 --drop (-d)           Drop and create Schema before loading. (default: false)
```

Example for loading single files using all available options:
```bash
$ php app/console h4cc_alice_fixtures:load:files --type=yaml --seed=42 --local=de_DE --persist=true --drop=true src/Acme/DemoBundle/Fixtures/Users.yml src/Acme/DemoBundle/Fixtures/Articles.yml
```

Preconfigured fixture sets can be loaded with this command:
```bash
$ php app/console h4cc_alice_fixtures:load:sets -h
Usage:
 h4cc_alice_fixtures:load:sets [sets1] ... [setsN]

Arguments:
 sets                  List of path to fixture sets to import.
```

Preconfigured FixtureSet:
```php
<?php

// Creating a fixture set with own configuration,
$set = new h4cc\AliceFixturesBundle\Fixtures\FixtureSet(array(
    'locale' => 'de_DE',
    'seed' => 42,
    'do_drop' => true,
    'do_persist' => true,
));

$set->addFile(__DIR__.'/Users.yml', 'yaml');
$set->addFile(__DIR__.'/Articles.yml', 'yaml');

return $set;
```
Such a file has to return a Object with the FixtureSetInterface Interface.


Example command for loading the FixtureSet:
```bash
$ php app/console h4cc_alice_fixtures:load:sets src/Acme/DemoBundle/Fixtures/UsersAndArticlesSet.php
```

### PHPUnit

If needed, the fixtures can also be loaded in a PHPUnit test.
Accessing the needed container in a Symfony2 envorinment is described here: http://symfony.com/doc/current/book/testing.html#accessing-the-container

Example:
```php
// Ensuring the same fixtures for each testcase.
public function setUp()
{
    $client = static::createClient();
    $manager = $client->getContainer()->get('h4cc_alice_fixtures.manager');
    $manager->load(require(__DIR__.'/fixtures/FixtureSet.php'));
}
```

### Selenium

This flexible way of handling data fixtures offers a easy way to work with selenium/behat/mink.
For example could you create a controller behind a development route, that is called by selenium to ensure a specific dataset.


### Adding own Providers for Faker

A provider for Faker can be any class, that has public methods.
These methods can be used in the fixture files for own testdata or even calculations.
To register a provider, create a service and tag it.

Example:
```yaml
services:
    your.faker.provider:
        class: YourProviderClass
        tags:
            -  { name: h4cc_alice_fixtures.provider }
```

### Adding own Processors for Alice

A alice processor can be used to manipulate a object _before_ and _after_ persisting.
To register a own processor, create a service and tag it.

Example:
```yaml
services:
    your.alice.processor:
        class: YourProcessorClass
        tags:
            -  { name: h4cc_alice_fixtures.processor }
```

## Future and ToDos:

- More Unit and functional tests
