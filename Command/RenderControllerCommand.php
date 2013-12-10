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

use Cocur\Bundle\BuildBundle\Renderer\ControllerRenderer;
use Cocur\Bundle\BuildBundle\Exception\ControllerNotFoundException;
use Cocur\Bundle\BuildBundle\Exception\RouteNotFoundException;

/**
 * RenderControllerCommand
 *
 * @package    CocurBuildBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class RenderControllerCommand extends Command
{
    /** @var ControllerRenderer */
    private $renderer;

    /**
     * Constructor.
     *
     * @param ControllerRenderer $renderer Controller renderer
     */
    public function __construct(ControllerRenderer $renderer)
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
            ->setName('cocur:render-controller')
            ->setDescription('Renders the HTML of the given controller')
            ->addArgument('controller', InputArgument::REQUIRED, 'Name of the controller to render.')
            ->addOption('base-url', null, InputOption::VALUE_REQUIRED, 'Base URL.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('base-url')) {
            $this->renderer->setBaseUrl($input->getOption('base-url'));
        }

        try {
            $this->renderer->render($input->getArgument('controller'));
        } catch (ControllerNotFoundException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        } catch (RouteNotFoundException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
