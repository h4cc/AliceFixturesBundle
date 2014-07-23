AliceFixturesBundle
===================

A Symfony2 bundle for flexible usage of [nelmio/alice](https://github.com/nelmio/alice) and [fzaninotto/Faker](https://github.com/fzaninotto/Faker) in Symfony2.

[![Build Status](https://travis-ci.org/h4cc/AliceFixturesBundle.png?branch=master)](https://travis-ci.org/h4cc/AliceFixturesBundle)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/h4cc/AliceFixturesBundle/badges/quality-score.png?s=2f90c394022338ad406685a575f6ac7ebcde2a2e)](https://scrutinizer-ci.com/g/h4cc/AliceFixturesBundle/)
[![Code Coverage](https://scrutinizer-ci.com/g/h4cc/AliceFixturesBundle/badges/coverage.png?s=9bf0abf8ef0ecf41d6187cb8cebca02520fb7150)](https://scrutinizer-ci.com/g/h4cc/AliceFixturesBundle/)
[![SensioLabsInsight](https://insight.sensiolabs.com/projects/bcd3cd50-845f-41d3-afbd-93e0a39f03c7/mini.png)](https://insight.sensiolabs.com/projects/bcd3cd50-845f-41d3-afbd-93e0a39f03c7)
[![Project Status](http://stillmaintained.com/h4cc/AliceFixturesBundle.png)](http://stillmaintained.com/h4cc/AliceFixturesBundle)


## Status

This Bundle should be considered "Work-In-Progress". Every version < 1.0 __can and will change__.
This also means, if you have a fundamental idea for a change, feel free to contribute.
Contributions in any form are always welcome!


## Introduction

The aim of this bundle is to provide a new way of working with data fixtures detached from the common Doctrine DataFixtures.
Loading of fixtures should be decoupled and easy to integrate where needed.
This bundle offers loading Fixtures from yaml and php files, also dropping and recreating the ORM Schema.
Next to Doctrine/ORM, also Doctrine/MongoDB-ODM is supported for recreating schema and persisting fixtures.

If you are searching for Bundle, that provides a way to integrate Alice with Doctrine DataFixtures, have a look at [hautelook/AliceBundle](https://github.com/hautelook/AliceBundle).

This bundle is also capable of recreating the ORM schema.
This means _all_ tables managed by Doctrine will be dropped and recreated. A data loss will appear, __you have been warned__.


## Installation

Simply require the bundle by its name with composer:
```bash
$ php composer.phar require h4cc/alice-fixtures-bundle
```
Follow the 'dev-master' branch for latest dev version. But i recommend to use more stable version tags if available.


After that, add the Bundle to your Kernel, most likely in the "dev" or "test" environment.
```php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
    );

    if (in_array($this->getEnvironment(), array('dev', 'test'))) {
        // ...
        $bundles[] = new h4cc\AliceFixturesBundle\h4ccAliceFixturesBundle();
    }
}
```
Keep in mind, that this bundle is capable of removing whole databases, so be carefull when using in production!


## Configuration

You can globally configure the Seed for random values, the Locale for Faker and a global flag do_flush,
if ORM flushes of entities should be omitted or not.

A custom 'object_manager' service id can be defined, as well a custom 'schema_manager' service id.

In case you want to use the default services for either doctrine/orm or doctrine/mongodb-odm, set the "doctrine" value accordingly, which can be either "orm" or "mongodb-odm".
This setting will just adjust the service-ids for 'object_manager' and 'schema_manager' accordingly, if you have not set them on your own.

```yaml
# app/config/config_dev.yml

h4cc_alice_fixtures:
    locale: en_US                               # default
    seed: 1                                     # default
    do_flush: true                              # default

    object_manager: null                        # default
    schema_tool: null                           # default
    doctrine: orm                               # default
```

Or you can use many managers:

```yaml
# app/config/config_dev.yml

h4cc_alice_fixtures:
    managers:
        orm:
            locale: en_US                               # default
            seed: 1                                     # default
            do_flush: true                              # default

            object_manager: null                        # default
            schema_tool: null                           # default
            doctrine: orm                               # default
        mongodb:
            locale: en_US
            seed: 1
            do_flush: true

            object_manager: null
            schema_tool: null
            doctrine: mongodb-odm
```

Check out this command for a always up-to-date configuration reference:
```
$ php app/console config:dump-reference h4cc_alice_fixtures
```


## Usage

### Fixtures

Fixtures are defined in YAML or PHP Files like [nelmio/alice](https://github.com/nelmio/alice) describes it.
The two alice default loaders 'Yaml' and 'Base' are already available.
If you want to integrate own loaders, patch the Loader Factory service for your needs ('h4cc_alice_fixtures.loader.factory').

Loading the entities from single fixture files can be done with the __FixtureManagerInterface::loadFiles(array $files, $type='yaml');__.
Persisting them can be done with __FixtureManagerInterface::persist(array $entities, $drop=false);__.

Example:

```php
$files = array(__DIR__.'/fixtures/Users.yml', __DIR__.'/fixtures/Articles.yml');
// Or use $manager = $this->get('h4cc_alice_fixtures.[my_manager_name]_manager'); to use an other manager.
$manager = $this->get('h4cc_alice_fixtures.manager');

// Step 1, load entities.
$objects = $manager->loadFiles($files, 'yaml');

// Manipulate or add own objects here ...

// Step 2, persist them.
$manager->persist($objects, true);
```

### Fixture Sets

A more advanced way of loading fixtures is using "FixtureSets".
Look at it like a fixture configuration object for multiple fixture files and options.

Example:
```php
$manager = $this->getContainer()->get('h4cc_alice_fixtures.manager');

// Get a FixtureSet with __default__ options.
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

return $set;
```

### Commands

There are some commands for loading fixtures included in this bundle.
They are also divided in loading plain files or FixtureSets.

```
h4cc_alice_fixtures
  h4cc_alice_fixtures:load:files        Load fixture files using alice and faker.
  h4cc_alice_fixtures:load:sets         Load fixture sets using alice and faker.
```

Example for loading single files using all available options:
```bash
$ php app/console h4cc_alice_fixtures:load:files --manager=default --type=yaml --seed=42 --local=de_DE --no-persist --drop src/Acme/DemoBundle/Fixtures/Users.yml src/Acme/DemoBundle/Fixtures/Articles.yml
```

Example command for loading the given FixtureSet:
```bash
$ php app/console h4cc_alice_fixtures:load:sets src/Acme/DemoBundle/Fixtures/UsersAndArticlesSet.php
```

When your FixtureSets are stored at the default path 'DataFixtures/Alice/' and are named like 'ExampleSet.php', they can be found automaticaly. Like the fixtures in Doctrine Datafixtures do.
To load the default fixture sets, simply execute this command:
```bash
$ php app/console h4cc_alice_fixtures:load:sets
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


## Demo Application

There is a Demo Application showing some ways to use this bundle: https://github.com/h4cc/AliceDemo

Feel free to add some more demos if you like.


### PHPUnit

If needed, the fixtures can also be loaded in a PHPUnit test.
Accessing the needed container in a Symfony2 environment is described here: http://symfony.com/doc/current/book/testing.html#accessing-the-container

Example:
```php
// Ensuring the same fixtures for each testcase.
public function setUp()
{
    // This may be slow ... have an eye on that.
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

## License

This bundle is licensed under MIT.
