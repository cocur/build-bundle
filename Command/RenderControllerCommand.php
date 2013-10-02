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

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * RenderControllerCommand
 *
 * @package    BcStaticSiteBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class RenderControllerCommand extends ContainerAwareCommand
{
    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this
            ->setName('bc:static-site:render-controller')
            ->setDescription('Renders the HTML of the given controller')
            ->addArgument('controller', InputArgument::REQUIRED, 'Name of the controller to render.');
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $renderer = $this->getContainer()->get('bc_static_site.renderer.controller');

        try {
            $renderer->render($input->getArgument('controller'));
        } catch (ControllerNotFoundException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        } catch (RouteNotFoundException $e) {
            $output->writeln(sprintf('<error>%s</error>', $e->getMessage()));
        }
    }
}
