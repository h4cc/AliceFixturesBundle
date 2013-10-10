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

use h4cc\AliceFixturesBundle\Fixtures\FixtureSetInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class LoadSetsCommand
 *
 * @author Julius Beckmann <github@h4cc.de>
 */
class LoadSetsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
        ->setName('h4cc_alice_fixtures:load:sets')
        ->setDescription('Load fixture sets using alice and faker.')
        ->addArgument('sets', InputArgument::IS_ARRAY, 'List of path to fixture sets to import.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sets = $input->getArgument('sets');

        if (!$sets) {
            $output->writeln("No sets to load");
        }

        // Check if all set files exist
        foreach ($sets as $file) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException("FixtureSet file does not exist: '$file'.");
            }
        }

        /**
         * @var $manager \h4cc\AliceFixturesBundle\Fixtures\FixtureManager
         */
        $manager = $this->getContainer()->get('h4cc_alice_fixtures.manager');

        foreach ($sets as $file) {
            $output->write("Loading file '$file' ... ");

            // The file should return a FixtureSetInterface
            $set = include($file);

            if (!$set || !($set instanceof FixtureSetInterface)) {
                throw new \InvalidArgumentException("File '$file' does not return a FixtureSetInterface.");
            }

            $entities = $manager->load($set);

            $output->writeln("loaded " . count($entities) . " entities ... done.");
        }
    }
}
