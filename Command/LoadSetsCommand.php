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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Finder\Finder;

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
          ->addArgument('sets', InputArgument::IS_ARRAY, 'List of path to fixture sets to import.')
          ->addOption('manager', 'm', InputOption::VALUE_OPTIONAL, 'The manager name to used.', 'default')
          ->addOption('drop', 'd', InputOption::VALUE_NONE, 'Drop and create Schema before loading.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $sets = $input->getArgument('sets');

        if (!$sets) {
            $sets = $this->findSetsByDefaultNaming();
        }

        if (!$sets) {
            $output->writeln("No sets to load");
        }

        // Check if all set files exist
        foreach ($sets as $file) {
            if (!file_exists($file)) {
                throw new \InvalidArgumentException("FixtureSet file does not exist: '$file'.");
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

        $fixtureSets = $this->loadFixtureSetsFromFiles($sets);

        $this->loadFixtureSets($fixtureSets, $manager, $output);
    }
    
    protected function loadSet($file)
    {
        return include $file;
    }

    /**
     * Returns a list of all *Bundle/DataFixtures/Alice/*Set.php files.
     *
     * @return string[]
     */
    protected function findSetsByDefaultNaming() {
        // Get all existing paths from bundles.
        $paths = array();
        /** @var $bundle \Symfony\Component\HttpKernel\Bundle\BundleInterface */
        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            if(is_dir($path = $bundle->getPath().'/DataFixtures/Alice')) {
                $paths[] = $path;
            }
        }

        if(!$paths) {
            return array();
        }

        // Find all Sets in these paths.
        $finder = new Finder();
        $finder->files()->name('*Set.php')->in($paths);

        // Return paths to sets.
        return array_keys(iterator_to_array($finder));
    }

    private function loadFixtureSetsFromFiles($sets)
    {
        $fixtureSets = array();

        foreach ($sets as $file) {

            // The file should return a FixtureSetInterface
            $set = $this->loadSet($file);

            if (!$set || !($set instanceof FixtureSetInterface)) {
                throw new \InvalidArgumentException("File '$file' does not return a FixtureSetInterface.");
            }

            $fixtureSets[$file] = $set;
        }

        return $this->orderFixtureSets($fixtureSets);
    }

    private function orderFixtureSets($fixtureSets)
    {
        uasort($fixtureSets, function(FixtureSetInterface $setA, FixtureSetInterface $setB) {
            $a = $setA->getOrder();
            $b = $setB->getOrder();
            if ($a == $b) {
                return 0;
            }
            return ($a < $b) ? -1 : 1;
        });

        return $fixtureSets;
    }

    private function loadFixtureSets($fixtureSets, $manager, $output)
    {
        // Store all loaded references in this array, so they can be used by other FixtureSets.
        $references = array();

        foreach ($fixtureSets as $file =>  $set) {

            $output->write("Loading file '$file' ... ");

            $entities = $manager->load($set, $references);

            // Only reusing loaded entities. Internal references are ignored because of intended private state.
            $references = array_merge($references, $entities);

            $output->writeln("loaded " . count($entities) . " entities ... done.");
        }
    }
}
