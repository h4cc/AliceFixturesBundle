<?php

/*
 * This file is part of the h4cc/AliceFixtureBundle package.
 *
 * (c) Julius Beckmann <github@h4cc.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace h4cc\AliceFixturesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoadFilesCommand
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class LoadFilesCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
          ->setName('h4cc_alice_fixtures:load:files')
          ->setDescription('Load fixture files using alice and faker.')
          ->addArgument('files', InputArgument::IS_ARRAY, 'List of files to import.')
          ->addOption('type', 't', InputOption::VALUE_OPTIONAL, 'Type of loader. Can be "yaml" or "php".', 'yaml')
          ->addOption('seed', null, InputOption::VALUE_OPTIONAL, 'Seed for random generator.', 1)
          ->addOption('locale', 'l', InputOption::VALUE_OPTIONAL, 'Locale for Faker provider.', 'en_EN')
          ->addOption('no-persist', 'np', InputOption::VALUE_NONE, 'Persist loaded entities in database.')
          ->addOption('drop', 'd', InputOption::VALUE_NONE, 'Drop and create Schema before loading.')
          ->addOption('manager', 'm', InputOption::VALUE_OPTIONAL, 'The manager name to used.', 'default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');
        $type = $input->getOption('type');

        if (!$files) {
            $output->writeln("No files to load");
        }

        // Check if all files exist
        foreach ($files as $file) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException("Fixture file does not exist: '$file'.");
            }
        }

        $managerServiceId = 'h4cc_alice_fixtures.manager';
        $schemaToolServiceId = 'h4cc_alice_fixtures.orm.schema_tool';

        if ('default' !== $input->getOption('manager')) {
            $managerServiceId    = sprintf('h4cc_alice_fixtures.%s_manager', $input->getOption('manager'));
            $schemaToolServiceId = sprintf('h4cc_alice_fixtures.orm.%s_schema_tool', $input->getOption('manager'));
        }

        /**
         * @var $manager \h4cc\AliceFixturesBundle\Fixtures\FixtureManager
         */
        $manager = $this->getContainer()->get($managerServiceId);

        if ($input->getOption('drop')) {
            $schemaTool = $this->getContainer()->get($schemaToolServiceId);
            $schemaTool->dropSchema();
            $schemaTool->createSchema();
        }

        foreach ($files as $file) {
            $output->write("Loading file '$file' ... ");

            $set = $manager->createFixtureSet();
            $set->addFile($file, $type);
            $set->setDoDrop(false); // Never drop while iterating over files.
            $set->setDoPersist(!$input->getOption('no-persist'));
            $set->setLocale($input->getOption('locale'));
            $set->setSeed($input->getOption('seed'));

            $entities = $manager->load($set);

            $output->writeln("loaded " . count($entities) . " entities ... done.");
        }
    }
}
