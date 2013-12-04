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

use Braincrafted\Bundle\StaticSiteBundle\Command\RenderRouteCommand;

/**
 * RenderRouteCommandTest
 *
 * @category   Test
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class RenderRouteCommandTest extends \PHPUnit_Framework_TestCase
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
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRouteCommand::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRouteCommand::configure()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRouteCommand::execute()
     */
    public function executeShouldRunCommand()
    {
        $renderer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer');
        $renderer->shouldReceive('setBaseUrl')->with('/base')->once();
        $renderer->shouldReceive('renderByName')->with('foobar')->once();

        // mock the Kernel or create one depending on your needs
        $application = new Application($this->kernel);
        $application->add(new RenderRouteCommand($renderer));

        $command = $application->find('braincrafted:static-site:render-route');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'    => $command->getName(),
            'route'      => 'foobar',
            '--base-url' => '/base'
        ]);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRouteCommand::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRouteCommand::configure()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderRouteCommand::execute()
     */
    public function executeShouldOutputErrorIfRouteNotFound()
    {
        $exception = new \Braincrafted\Bundle\StaticSiteBundle\Exception\RouteNotFoundException(
            'There is no route "foobar"'
        );
        $renderer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer');
        $renderer
            ->shouldReceive('renderByName')
            ->with('foobar')
            ->andThrow($exception);

        // mock the Kernel or create one depending on your needs
        $application = new Application($this->kernel);
        $application->add(new RenderRouteCommand($renderer));

        $command = $application->find('braincrafted:static-site:render-route');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName(), 'route' => 'foobar' ]);

        $this->assertRegExp('/There is no route "foobar"/', $commandTester->getDisplay());
    }
}
