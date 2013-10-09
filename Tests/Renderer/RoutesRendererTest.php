<?php

namespace Bc\Bundle\StaticSiteBundle\Tests\Renderer;

use \Mockery as m;

use Bc\Bundle\StaticSiteBundle\Renderer\RoutesRenderer;

/**
 * RoutesRendererTest
 *
 * @group unti
 */
class RoutesRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var RoutesRenderer */
    private $renderer;

    /** @var Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer */
    private $routeRenderer;

    /** @var Symfony\Component\Routing\Router */
    private $router;

    public function setUp()
    {
        $this->routeRenderer = m::mock('Bc\Bundle\StaticSiteBundle\Renderer\RouteRenderer');
        $this->router = m::mock('Symfony\Component\Routing\Router');

        $this->renderer = new RoutesRenderer($this->routeRenderer, $this->router);
    }

    /**
     * Tests the render() method.
     *
     * Route collection returns two routes, one public and one private (prefixed with "_"), renderer
     * should only render the public route.
     *
     * @covers Bc\Bundle\StaticSiteBundle\Renderer\RoutesRenderer::render()
     */
    public function testRender()
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
