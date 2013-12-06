<?php

/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

use Braincrafted\Bundle\StaticSiteBundle\Renderer\RoutesRenderer;

/**
 * BuildCommand
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class CleanCommand extends Command
{
    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $buildDirectory;

    /**
     * @param Filesystem $filesystem
     * @param string     $buildDirectory
     */
    public function __construct(Filesystem $filesystem, $buildDirectory)
    {
        $this->filesystem     = $filesystem;
        $this->buildDirectory = $buildDirectory;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('braincrafted:static-site:clean')
            ->setDescription('Cleans the static site.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $count = $this->cleanDirectory(
            (new Finder)->in($this->buildDirectory),
            $output
        );

        $output->writeln(sprintf(
            'Removed <info>%d</info> files from <comment>%s</comment>.',
            $count,
            $this->buildDirectory
        ));
    }

    /**
     * Iteratores over all given files and deletes them.
     *
     * @param Finder          $finder
     * @param OutputInterface $output
     *
     * @return integer Number of deleted files
     */
    protected function cleanDirectory(Finder $finder, OutputInterface $output)
    {
        $count = 0;
        $files = [];

        // Finder and Filesystem doen't really work well when we delete directories while we iterate through them.
        // That's why we iterate over all files and store the path in an array
        foreach ($finder as $file) {
            $files[] = $file->getPathname();
        }

        foreach ($files as $file) {
            if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
                $output->writeln(sprintf('Delete: <comment>%s</comment>', $file));
            }
            $this->filesystem->remove($file);
            $count += 1;
        }

        return $count;
    }
}
