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

use Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand;

/**
 * RenderControllerCommandTest
 *
 * @category   Test
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class RenderControllerCommandTest extends \PHPUnit_Framework_TestCase
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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::configure()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::execute()
     */
    public function executeShouldRunCommand()
    {
        $renderer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer');
        $renderer->shouldReceive('setBaseUrl')->with('/base')->once();
        $renderer->shouldReceive('render')->with('foobar')->once();

        // mock the Kernel or create one depending on your needs
        $application = new Application($this->kernel);
        $application->add(new RenderControllerCommand($renderer));

        $command = $application->find('braincrafted:static-site:render-controller');
        $commandTester = new CommandTester($command);
        $commandTester->execute([
            'command'    => $command->getName(),
            'controller' => 'foobar',
            '--base-url' => '/base'
        ]);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::configure()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::execute()
     */
    public function executeShouldOutputErrorIfControllerNotFound()
    {
        $exception = new \Braincrafted\Bundle\StaticSiteBundle\Exception\ControllerNotFoundException(
            'Could not find controller "foobar"'
        );
        $renderer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer');
        $renderer
            ->shouldReceive('render')
            ->with('foobar')
            ->andThrow($exception);

        // mock the Kernel or create one depending on your needs
        $application = new Application($this->kernel);
        $application->add(new RenderControllerCommand($renderer));

        $command = $application->find('braincrafted:static-site:render-controller');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName(), 'controller' => 'foobar' ]);

        $this->assertRegExp('/Could not find controller "foobar"/', $commandTester->getDisplay());
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::configure()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Command\RenderControllerCommand::execute()
     */
    public function executeShouldOutputErrorIfRouteNotFound()
    {
        $exception = new \Braincrafted\Bundle\StaticSiteBundle\Exception\RouteNotFoundException(
            'Could not find route for controller "foobar"'
        );
        $renderer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer');
        $renderer
            ->shouldReceive('render')
            ->with('foobar')
            ->andThrow($exception);

        // mock the Kernel or create one depending on your needs
        $application = new Application($this->kernel);
        $application->add(new RenderControllerCommand($renderer));

        $command = $application->find('braincrafted:static-site:render-controller');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName(), 'controller' => 'foobar' ]);

        $this->assertRegExp('/Could not find route for controller "foobar"/', $commandTester->getDisplay());
    }
}
