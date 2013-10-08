<?php

namespace h4cc\AliceFixturesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
        ->addOption('persist', 'p', InputOption::VALUE_OPTIONAL, 'Persist loaded entities in database.', true)
        ->addOption('drop', 'd', InputOption::VALUE_OPTIONAL, 'Drop and create Schema before loading.', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $files = $input->getArgument('files');
        $type = $input->getOption('type');

        if(!$files) {
            $output->writeln("No files to load");
        }

        // Check if all files exist
        foreach($files as $file) {
            if(!file_exists($file)) {
                throw new \InvalidArgumentException("Fixture file does not exist: '$file'.");
            }
        }

        /**
         * @var $manager \h4cc\AliceFixturesBundle\Fixtures\FixtureManager
         */
        $manager = $this->getContainer()->get('h4cc_alice_fixtures.manager');

        if($input->getOption('drop')) {
            $schemaTool = $manager->getSchemaTool();
            $schemaTool->dropSchema();
            $schemaTool->createSchema();
        }

        foreach($files as $file) {
            $output->write("Loading file '$file' ... ");

            $set = $manager->createFixtureSet();
            $set->addFile($file, $type);
            $set->setDoDrop(false); // Never drop while iterating over files.
            $set->setDoPersist($input->getOption('persist'));
            $set->setLocale($input->getOption('type'));
            $set->setSeed($input->getOption('seed'));

            $entities = $manager->load($set);

            $output->writeln("loaded ".count($entities)." entities ... done.");
        }
    }
}
