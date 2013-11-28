<?php

/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Tests\Command;

use \Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Braincrafted\Bundle\StaticSiteBundle\Command\RenderRoutesCommand;

/**
 * RenderRoutesCommandTest
 *
 * @category   Test
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class RenderRoutesCommandTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->kernel = m::mock('Symfony\Component\HttpKernel\KernelInterface');
        $this->kernel->shouldReceive('getName')->andReturn('app');
        $this->kernel->shouldReceive('getEnvironment')->andReturn('prod');
        $this->kernel->shouldReceive('isDebug')->andReturn(false);
    }

    public function tearDown()
    {
    }

    /**
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRoutesCommand::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRoutesCommand::configure()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRoutesCommand::execute()
     */
    public function testExecute()
    {
        $renderer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Renderer\RoutesRenderer');
        $renderer->shouldReceive('setBaseUrl')->with('/base')->once();
        $renderer->shouldReceive('render')->andReturn(3)->once();

        // mock the Kernel or create one depending on your needs
        $application = new Application($this->kernel);
        $application->add(new RenderRoutesCommand($renderer));

        $command = $application->find('braincrafted:static-site:render-routes');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'    => $command->getName(),
            '--base-url' => '/base'
        ]);

        $this->assertRegExp('/Rendered 3 routes\./', $commandTester->getDisplay());
    }
}
