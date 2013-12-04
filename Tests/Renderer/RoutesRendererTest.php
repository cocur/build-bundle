<?php

namespace Braincrafted\Bundle\StaticSiteBundle\Tests\Renderer;

use \Mockery as m;

use Braincrafted\Bundle\StaticSiteBundle\Renderer\RoutesRenderer;

/**
 * RoutesRendererTest
 *
 * @group unti
 */
class RoutesRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var RoutesRenderer */
    private $renderer;

    /** @var Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer */
    private $routeRenderer;

    /** @var Symfony\Component\Routing\Router */
    private $router;

    public function setUp()
    {
        $this->routeRenderer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer');
        $this->router = m::mock('Symfony\Component\Routing\Router');

        $this->renderer = new RoutesRenderer($this->routeRenderer, $this->router);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RoutesRenderer::setBaseUrl()
     */
    public function setBaseUrlShouldPropagateBaseUrlToRouteRenderer()
    {
        $this->routeRenderer->shouldReceive('setBaseUrl')->with('/my')->once();

        $this->renderer->setBaseUrl('/my');
    }

    /**
     * Tests the render() method.
     *
     * Route collection returns two routes, one public and one private (prefixed with "_"), renderer
     * should only render the public route.
     *
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RoutesRenderer::render()
     */
    public function renderShouldRenderRoutes()
    {
        $route = m::mock('Symfony\Component\Routing\Route');

        $routeCollection = m::mock('Symfony\Component\Routing\RouteCollection');
        $routeCollection
            ->shouldReceive('all')
            ->once()
            ->andReturn([ 'route1' => $route, '_route2' => $route]);

        $this->router
            ->shouldReceive('getRouteCollection')
            ->once()
            ->andReturn($routeCollection);

        $this->routeRenderer
            ->shouldReceive('render')
            ->with($route)
            ->once();

        $result = $this->renderer->render();

        $this->assertEquals(1, $result);
    }
}
