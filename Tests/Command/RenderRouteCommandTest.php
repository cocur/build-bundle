<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Tests\Command;

use \Mockery as m;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;

use Cocur\Bundle\BuildBundle\Command\RenderRouteCommand;

/**
 * RenderRouteCommandTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Command
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class RenderRouteCommandTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    private $application;

    public function setUp()
    {
        $this->application = new Application($this->getMockKernel());
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Command\RenderRouteCommand::__construct()
     * @covers Cocur\Bundle\BuildBundle\Command\RenderRouteCommand::configure()
     * @covers Cocur\Bundle\BuildBundle\Command\RenderRouteCommand::execute()
     */
    public function executeShouldRunCommand()
    {
        $renderer = m::mock('Cocur\Bundle\BuildBundle\Renderer\RouteRenderer');
        $renderer->shouldReceive('setBaseUrl')->with('/base')->once();
        $renderer->shouldReceive('renderByName')->with('foobar')->once();

        $this->application->add(new RenderRouteCommand($renderer));

        $command = $this->application->find('cocur:render-route');
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
     * @covers Cocur\Bundle\BuildBundle\Command\RenderRouteCommand::__construct()
     * @covers Cocur\Bundle\BuildBundle\Command\RenderRouteCommand::configure()
     * @covers Cocur\Bundle\BuildBundle\Command\RenderRouteCommand::execute()
     */
    public function executeShouldOutputErrorIfRouteNotFound()
    {
        $exception = new \Cocur\Bundle\BuildBundle\Exception\RouteNotFoundException(
            'There is no route "foobar"'
        );
        $renderer = m::mock('Cocur\Bundle\BuildBundle\Renderer\RouteRenderer');
        $renderer
            ->shouldReceive('renderByName')
            ->with('foobar')
            ->andThrow($exception);

        $this->application->add(new RenderRouteCommand($renderer));

        $command = $this->application->find('cocur:render-route');
        $commandTester = new CommandTester($command);
        $commandTester->execute([ 'command' => $command->getName(), 'route' => 'foobar' ]);

        $this->assertRegExp('/There is no route "foobar"/', $commandTester->getDisplay());
    }

    /**
     * @return Symfony\Component\HttpKernel\KernelInterface
     */
    protected function getMockKernel()
    {
        $kernel = m::mock('Symfony\Component\HttpKernel\KernelInterface');
        $kernel->shouldReceive('getName')->andReturn('app');
        $kernel->shouldReceive('getEnvironment')->andReturn('prod');
        $kernel->shouldReceive('isDebug')->andReturn(false);

        return $kernel;
    }
}
