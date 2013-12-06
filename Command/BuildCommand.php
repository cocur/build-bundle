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
 */
class BuildCommand extends Command
{
    /** @var RoutesRenderer */
    private $renderer;

    /** @var string */
    private $buildDirectory;

    /** @var string */
    private $baseUrl;

    /**
     * Constructor.
     *
     * @param string $buildDirectory
     */
    public function __construct(RoutesRenderer $renderer, $buildDirectory, $baseUrl)
    {
        $this->renderer       = $renderer;
        $this->buildDirectory = $buildDirectory;
        $this->baseUrl        = $baseUrl;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('braincrafted:static-site:build')
            ->setDescription('Builds the static site.')
            ->addOption('base-url', null, InputOption::VALUE_REQUIRED, 'Base URL', null);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ('prod' === $input->getOption('env')) {
            $this->executeCacheClear($input, $output);
        }

        if ($input->getOption('base-url')) {
            $this->renderer->setBaseUrl($input->getOption('base-url'));
        }

        $counter = $this->renderer->render();
        $output->writeln(sprintf('Rendered <info>%s</info> routes.', $counter));

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
        $arguments = [ '', '--env' => $input->getOption('env') ];

        $returnCode = $this->executeCommand($command, $arguments, clone $output);

        if (0 === $returnCode) {
            $output->writeln('<comment>Cleared cache.</comment>');
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
        $arguments = [
            'write_to'  => $this->buildDirectory.$this->getBaseUrl($input),
            '--env'     => $input->getOption('env'),
        ];

        $returnCode = $this->executeCommand($command, $arguments, clone $output);

        if (0 === $returnCode) {
            $output->writeln('<comment>Dumped assets into build directory.</comment>');
        } else {
            $output->writeln('<error>Erroring dumping assets into build directory.</error>');
        }
    }

    /**
     * @param Command         $command
     * @param array           $arguments
     * @param OutputInterface $output
     *
     * @return integer Exit code
     */
    protected function executeCommand(Command $command, array $arguments, OutputInterface $output)
    {
        $output = clone $output;
        if ($output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE) {
            $output->setVerbosity($output->getVerbosity());
        } else {
            $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }

        return $command->run(new ArrayInput($arguments), $output);
    }

    protected function getBaseUrl(InputInterface $input)
    {
        if ($input->getOption('base-url')) {
            return $input->getOption('base-url');
        }

        return $this->baseUrl;
    }
}
