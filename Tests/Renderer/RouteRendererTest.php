<?php

namespace Bc\Bundle\StaticSiteBundle\Tests\Renderer;

use \Mockery as m;

use Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer;

/**
 * RouteRendererTest
 *
 * @group unit
 */
class RouteRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouteRenderer */
    private $renderer;

    /** @var Symfony\Component\HttpKernel\Kernel */
    private $kernel;

    /** @var Symfony\Component\Routing\Router */
    private $router;

    /** @var string */
    private $buildDirectory;

    public function setUp()
    {
        $this->kernel = m::mock('Symfony\Component\HttpKernel\Kernel');
        $this->router = m::mock('Symfony\Component\Routing\Router');
        $this->writer = m::mock('Bc\Bundle\StaticSiteBundle\Writer\WriterInterface');

        $this->renderer = new RouteRenderer($this->kernel, $this->router, $this->writer);
    }

    /**
     * Tests the render() method.
     *
     * The render() method creates a request based on the given route and lets the kernel handle the request. The
     * response is saved to disk.
     *
     * @covers Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer::render()
     * @covers Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer::buildRequest()
     */
    public function testRender()
    {
        $route = m::mock('Symfony\Component\Routing\Route');
        $route->shouldReceive('getPattern')->times(3)->andReturn('/index.html');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->once()->andReturn('Foobar!');

        $this->kernel->shouldReceive('handle')->with(m::any())->once()->andReturn($response);
        $this->kernel->shouldReceive('terminate')->with(m::any(), $response)->once();
        $this->kernel->shouldReceive('shutdown')->once();

        $this->writer->shouldReceive('write')->with('/index.html', 'Foobar!')->once();

        $this->renderer->render($route);
    }

    /**
     * Tests the renderByName() method.
     *
     * @covers Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer::renderByName()
     * @covers Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer::render()
     * @covers Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer::buildRequest()
     * @covers Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer::getRoute()
     */
    public function testRenderByName()
    {
        $route = m::mock('Symfony\Component\Routing\Route');
        $route->shouldReceive('getPattern')->times(3)->andReturn('/index.html');

        $routeCollection = m::mock('Symfony\Component\Routing\RouteCollection');
        $routeCollection->shouldReceive('get')->with('index_route')->once()->andReturn($route);

        $this->router->shouldReceive('getRouteCollection')->once()->andReturn($routeCollection);

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->once()->andReturn('Foobar!');

        $this->kernel->shouldReceive('handle')->with(m::any())->once()->andReturn($response);
        $this->kernel->shouldReceive('terminate')->with(m::any(), $response)->once();
        $this->kernel->shouldReceive('shutdown')->once();

        $this->writer->shouldReceive('write')->with('/index.html', 'Foobar!')->once();

        $this->renderer->renderByName('index_route');
    }

    /**
     * Tests the renderByName() method, but the route is not found.
     *
     * @covers Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer::renderByName()
     *
     * @expectedException Bc\Bundle\StaticSiteBundle\Exception\RouteNotFoundException
     */
    public function testRenderByNameRouteNotFound()
    {
        $route = m::mock('Symfony\Component\Routing\Route');

        $routeCollection = m::mock('Symfony\Component\Routing\RouteCollection');
        $routeCollection->shouldReceive('get')->with('invalid_route')->once()->andReturn(null);

        $this->router->shouldReceive('getRouteCollection')->once()->andReturn($routeCollection);

        $this->renderer->renderByName('invalid_route');
    }
}
