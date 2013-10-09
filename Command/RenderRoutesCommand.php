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

use Bc\Bundle\StaticSiteBundle\Renderer\RoutesRenderer;

/**
 * RenderRoutesCommand
 *
 * @package    BcStaticSiteBundle
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
            ->setName('bc:static-site:render-routes')
            ->setDescription('Renders the HTML of all routes');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $counter = $this->renderer->render();
        $output->writeln(sprintf('Rendered <info>%s</info> routes.', $counter));
    }
}
