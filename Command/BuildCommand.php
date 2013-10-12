<?php
/**
 * This file is part of BcStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bc\Bundle\StaticSiteBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

use Bc\Bundle\StaticSiteBundle\Renderer\RoutesRenderer;

/**
 * BuildCommand
 *
 * @package    BcStaticSiteBundle
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
        $this
            ->setName('bc:static-site:build')
            ->setDescription('Builds the static site.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $counter = $this->renderer->render();
        $output->writeln(sprintf('Rendered <info>%s</info> routes.', $counter));

        $process = new Process(sprintf('php app/console assetic:dump %s', $this->buildDirectory));
        $process->run(function ($type, $buffer) use ($input, $output) {
            if (true === $output->isVerbose()) {
                $output->writeln(sprintf('<comment>%s</comment>', $buffer));
            }
        });

        if ($process->isSuccessful()) {
            $output->writeln('<info>Dumped assets into build directory.</info>');
        } else {
            $output->writeln('<error>Erroring dumping assets into build directory.</error>');
        }
    }
}
