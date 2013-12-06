<?php
/**
 * This file is part of BraincraftedCocurBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\CocurBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Braincrafted\Bundle\CocurBundle\Renderer\RoutesRenderer;

/**
 * BuildCommand
 *
 * @package    BraincraftedCocurBundle
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

    /** @var boolean */
    private $enableAssetic;

    /**
     * Constructor.
     *
     * @param string $buildDirectory
     * @param array  $options        Array with options; possible options: build_directory, base_url and enable_assetic.
     *
     * @throws \InvalidArgumentException if the option "build_directory" is missing.
     */
    public function __construct(RoutesRenderer $renderer, array $options)
    {
        if (false === isset($options['build_directory'])) {
            throw new \InvalidArgumentException('The option "build_directory" is missing.');
        }

        $this->renderer       = $renderer;
        $this->buildDirectory = $options['build_directory'];
        $this->baseUrl        = true === isset($options['base_url']) ? $options['base_url'] : '';
        $this->enableAssetic  = true === isset($options['enable_assetic']) ? $options['enable_assetic'] : false;

        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cocur:build')
            ->setDescription('Builds the static site.')
            ->addOption('base-url', null, InputOption::VALUE_REQUIRED, 'Base URL', null);
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('base-url')) {
            $this->renderer->setBaseUrl($input->getOption('base-url'));
        }

        $counter = $this->renderer->render();
        $output->writeln(sprintf('Rendered <info>%s</info> routes.', $counter));

        if (true === $this->enableAssetic) {
            $this->executeAsseticDump($input, $output);
        }
        $this->executeAssetsInstall($input, $output);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function executeAssetsInstall(InputInterface $input, OutputInterface $output)
    {
        $command = $this->getApplication()->find('assets:install');
        $arguments = [
            'target' => sprintf('%s%s', $this->buildDirectory, $this->getBaseUrl($input)),
            '--env'  => $input->getOption('env')
        ];

        $returnCode = $this->executeCommand($command, $arguments, clone $output);

        if (0 === $returnCode) {
            $output->writeln('<comment>Installed assets in build directory.</comment>');

            return;
        }

        $output->writeln('<error>Error installing assets in build directory.</error>');
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
            'write_to'  => sprintf('%s%s', $this->buildDirectory, $this->getBaseUrl($input)),
            '--env'     => $input->getOption('env')
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

    /**
     * @param InputInterface $input
     *
     * @return string
     */
    protected function getBaseUrl(InputInterface $input)
    {
        if ($input->getOption('base-url')) {
            return $input->getOption('base-url');
        }

        return $this->baseUrl;
    }
}
