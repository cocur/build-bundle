<?php
/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

use Cocur\Bundle\BuildBundle\Renderer\RoutesRenderer;

/**
 * RenderRoutesCommand
 *
 * @package    CocurBuildBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class RenderRoutesCommand extends Command
{
    /** @var RouteRenderer */
    private $renderer;

    /**
     * Constructor.
     *
     * @param RoutesRenderer $renderer Routes renderer
     */
    public function __construct(RoutesRenderer $renderer)
    {
        $this->renderer = $renderer;
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('cocur:render-routes')
            ->setDescription('Renders the HTML of all routes')
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
    }
}
