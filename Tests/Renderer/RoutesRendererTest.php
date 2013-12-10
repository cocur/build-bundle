<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Tests\Renderer;

use \Mockery as m;

use Cocur\Bundle\BuildBundle\Renderer\RoutesRenderer;

/**
 * RoutesRendererTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Renderer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class RoutesRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var RoutesRenderer */
    private $renderer;

    /** @var Cocur\Bundle\BuildBundle\Renderer\RouteRenderer */
    private $routeRenderer;

    /** @var Symfony\Component\Routing\Router */
    private $router;

    public function setUp()
    {
        $this->routeRenderer = m::mock('Cocur\Bundle\BuildBundle\Renderer\RouteRenderer');
        $this->router = m::mock('Symfony\Component\Routing\Router');

        $this->renderer = new RoutesRenderer($this->routeRenderer, $this->router);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Renderer\RoutesRenderer::setBaseUrl()
     */
    public function setBaseUrlShouldPropagateBaseUrlToRouteRenderer()
    {
        $this->routeRenderer->shouldReceive('setBaseUrl')->with('/my')->once();

        $this->renderer->setBaseUrl('/my');
    }

    /**
     * Tests the render() method with respect to the given list of routes.
     *
     * Route collection returns two routes, one public and one private (prefixed with "_"), renderer
     * should only render the public route.
     *
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Renderer\RoutesRenderer::render()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RoutesRenderer::getRoutes()
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
            ->with($route, 'route1')
            ->once();

        $result = $this->renderer->render();

        $this->assertEquals(1, $result);
    }

    /**
     * Tests the render() method.
     *
     * Route collection returns two routes, one public and one private (prefixed with "_"), renderer
     * should only render the public route.
     *
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Renderer\RoutesRenderer::render()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RoutesRenderer::getRoutes()
     */
    public function renderShouldRenderRoutesThatArePassedToTheConstructor()
    {
        $route = m::mock('Symfony\Component\Routing\Route');

        $routeCollection = m::mock('Symfony\Component\Routing\RouteCollection');
        $routeCollection->shouldReceive('get')->with('route1')->once()->andReturn($route);

        $this->router->shouldReceive('getRouteCollection')->once()->andReturn($routeCollection);
        $this->routeRenderer->shouldReceive('render')->with($route, 'route1')->once();

        $renderer = new RoutesRenderer($this->routeRenderer, $this->router, [ 'route1' ]);
        $result = $renderer->render();

        $this->assertEquals(1, $result);
    }
}
