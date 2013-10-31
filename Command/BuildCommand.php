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

use Braincrafted\Bundle\StaticSiteBundle\Renderer\RoutesRenderer;

/**
 * BuildCommand
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 *
 * @codeCoverageIgnore
 */
class BuildCommand extends Command
{
    /** @var RoutesRenderer */
    private $renderer;

    /** @var string */
    private $buildDirectory;

    /**
     * Constructor.
     *
     * @param string $buildDirectory
     */
    public function __construct(RoutesRenderer $renderer, $buildDirectory)
    {
        $this->renderer = $renderer;
        $this->buildDirectory = $buildDirectory;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->setName('braincrafted:static-site:build')
            ->setDescription('Builds the static site.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('prod' === $input->getOption('env')) {
            $this->executeCacheClear($input, $output);
        }
        $counter = $this->renderer->render();
        $output->writeln(sprintf("Rendered <info>%s</info> routes.\n", $counter));

        $this->executeAsseticDump($input, $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function executeCacheClear(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('cache:clear');
        $arguments = array(
            '--env' => 'prod',
            ''
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        if (0 === $returnCode) {
            $output->writeln("<info>Cleared cache.</info>\n");
        } else {
            $output->writeln("<error>Error clearing cache.</error>\n");
        }
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function executeAsseticDump(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('assetic:dump');
        $arguments = array(
            'write_to' => $this->buildDirectory,
            '--env'    => $input->getOption('env')
        );
        $input = new ArrayInput($arguments);
        $returnCode = $command->run($input, $output);

        if (0 === $returnCode) {
            $output->writeln('<info>Dumped assets into build directory.</info>');
        } else {
            $output->writeln('<error>Erroring dumping assets into build directory.</error>');
        }
    }
}
