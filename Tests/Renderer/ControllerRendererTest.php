<?php

/**
 * This file is part of BraincraftedCocurBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\CocurBundle\Tests\Renderer;

use \Mockery as m;

use Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer;

/**
 * ControllerRendererTest
 *
 * @category   Test
 * @package    BraincraftedCocurBundle
 * @subpackage Renderer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class ControllerRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var ControllerRenderer */
    private $renderer;

    /** @var Braincrafted\Bundle\CocurBundle\Renderer\RouteRenderer */
    private $routeRenderer;

    /** @var Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser */
    private $nameParser;

    /** @var Symfony\Component\Routing\Router */
    private $router;

    public function setUp()
    {
        $this->routeRenderer = m::mock('Braincrafted\Bundle\CocurBundle\Renderer\RouteRenderer');
        $this->nameParser = m::mock('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser');
        $this->router = m::mock('Symfony\Component\Routing\Router');

        $this->renderer = new ControllerRenderer($this->routeRenderer, $this->nameParser, $this->router);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::setBaseUrl()
     */
    public function setBaseUrlShouldSetBaseUrl()
    {
        $this->routeRenderer->shouldReceive('setBaseUrl')->with('/my')->once();

        $this->renderer->setBaseUrl('/my');
    }

    /**
     * Tests the render() method.
     *
     * The given controller exists and there is also a route for the controller. Thus, the render() method of the
     * RouteRenderer should be called.
     *
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::render()
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::getControllerName()
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::getRoute()
     */
    public function renderShouldRenderController()
    {
        $this->nameParser
            ->shouldReceive('parse')
            ->once()
            ->andReturn('Acme\DemoBundle\Controller\DefaultController::indexAction');

        $route = m::mock('Symfony\Component\Routing\Route');
        $route
            ->shouldReceive('getDefault')
            ->with('_controller')
            ->once()
            ->andReturn('Acme\DemoBundle\Controller\DefaultController::indexAction');

        $routeCollection = m::mock('Symfony\Component\Routing\RouteCollection');
        $routeCollection->shouldReceive('all')->andReturn([ 'route1' => $route ]);

        $this->router->shouldReceive('getRouteCollection')->once()->andReturn($routeCollection);

        $this->routeRenderer->shouldReceive('render')->with($route, 'route1')->once();

        $this->renderer->render('AcmeDemoBundle:Default:index');
    }

    /**
     * Tests the render() method when the controller does not exist.
     *
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::render()
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::getControllerName()
     *
     * @expectedException Braincrafted\Bundle\CocurBundle\Exception\ControllerNotFoundException
     */
    public function renderShouldThrowExceptionIfControllerNotFound()
    {
        $this->nameParser->shouldReceive('parse')->once()->andThrow(new \Exception);

        $this->renderer->render('AcmeDemoBundle:Default:notExisting');
    }

    /**
     * Tests the render() method when the route does not exist.
     *
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::render()
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::getControllerName()
     * @covers Braincrafted\Bundle\CocurBundle\Renderer\ControllerRenderer::getRoute()
     *
     * @expectedException Braincrafted\Bundle\CocurBundle\Exception\RouteNotFoundException
     */
    public function renderShouldThrowExceptionIfRouteNotFound()
    {
        $this->nameParser
            ->shouldReceive('parse')
            ->once()
            ->andReturn('Acme\DemoBundle\Controller\DefaultController::indexAction');

        $routeCollection = m::mock('Symfony\Component\Routing\RouteCollection');
        $routeCollection->shouldReceive('all')->once()->andReturn([ ]);

        $this->router->shouldReceive('getRouteCollection')->once()->andReturn($routeCollection);

        $this->renderer->render('AcmeDemoBundle:Default:index');
    }
}
