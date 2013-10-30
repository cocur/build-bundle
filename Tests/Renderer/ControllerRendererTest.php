<?php

namespace Braincrafted\Bundle\StaticSiteBundle\Tests\Renderer;

use \Mockery as m;

use Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer;

/**
 * ControllerRendererTest
 *
 * @group unti
 */
class ControllerRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var ControllerRenderer */
    private $renderer;

    /** @var Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer */
    private $routeRenderer;

    /** @var Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser */
    private $nameParser;

    /** @var Symfony\Component\Routing\Router */
    private $router;

    public function setUp()
    {
        $this->routeRenderer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer');
        $this->nameParser = m::mock('Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser');
        $this->router = m::mock('Symfony\Component\Routing\Router');

        $this->renderer = new ControllerRenderer($this->routeRenderer, $this->nameParser, $this->router);
    }

    /**
     * Tests the render() method.
     *
     * The given controller exists and there is also a route for the controller. Thus, the render() method of the
     * RouteRenderer should be called.
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer::render()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer::getControllerName()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer::getRoute()
     */
    public function testRender()
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
        $routeCollection->shouldReceive('all')->andReturn([ $route ]);

        $this->router->shouldReceive('getRouteCollection')->once()->andReturn($routeCollection);

        $this->routeRenderer->shouldReceive('render')->with($route)->once();

        $this->renderer->render('AcmeDemoBundle:Default:index');
    }

    /**
     * Tests the render() method when the controller does not exist.
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer::render()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer::getControllerName()
     *
     * @expectedException Braincrafted\Bundle\StaticSiteBundle\Exception\ControllerNotFoundException
     */
    public function testRenderControllerDoesNotExist()
    {
        $this->nameParser->shouldReceive('parse')->once()->andThrow(new \Exception);

        $this->renderer->render('AcmeDemoBundle:Default:notExisting');
    }

    /**
     * Tests the render() method when the route does not exist.
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer::render()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer::getControllerName()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\ControllerRenderer::getRoute()
     *
     * @expectedException Braincrafted\Bundle\StaticSiteBundle\Exception\RouteNotFoundException
     */
    public function testRenderRouteDoesNotExist()
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
