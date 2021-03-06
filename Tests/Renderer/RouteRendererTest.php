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

use Cocur\Bundle\BuildBundle\Renderer\RouteRenderer;

/**
 * RouteRendererTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Renderer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class RouteRendererTest extends \PHPUnit_Framework_TestCase
{
    /** @var RouteRenderer */
    private $renderer;

    /** @var Symfony\Component\HttpKernel\Kernel */
    private $kernel;

    /** @var Symfony\Component\Routing\Router */
    private $router;

    /** @var Cocur\Bundle\BuildBundle\Generator\GeneratorCollection */
    private $generatorCollection;

    /** @var string */
    private $buildDirectory;

    public function setUp()
    {
        $context = m::mock('Symfony\Component\Routing\RequestContext');
        $context->shouldReceive('setBaseUrl');

        $this->kernel = m::mock('Symfony\Component\HttpKernel\Kernel');
        $this->kernel->shouldReceive('getRootDir')->andReturn('/');
        $this->router = m::mock('Symfony\Component\Routing\Router');
        $this->router->shouldReceive('getContext')->andReturn($context);
        $this->writer = m::mock('Cocur\Bundle\BuildBundle\Writer\WriterInterface');
        $this->generatorCollection = m::mock('Cocur\Bundle\BuildBundle\Generator\GeneratorCollection');

        $this->renderer = new RouteRenderer($this->kernel, $this->router, $this->writer, $this->generatorCollection);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::setBaseUrl()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::getBaseUrl()
     */
    public function setBaseUrlShouldSetBaseUrl()
    {
        $this->renderer->setBaseUrl('my/');
        $this->assertEquals('/my', $this->renderer->getBaseUrl());
    }

    /**
     * Tests the render() method.
     *
     * The render() method creates a request based on the given route and lets the kernel handle the request. The
     * response is saved to disk.
     *
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::render()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::renderWithParameters()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::buildRequest()
     */
    public function renderShouldRenderRoute()
    {
        $this->generatorCollection->shouldReceive('has')->never()->andReturn(false);
        $this->router->shouldReceive('generate')->never();

        $route = m::mock('Symfony\Component\Routing\Route');
        $route->shouldReceive('getPath')->times(2)->andReturn('/index.html');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->once()->andReturn('Foobar!');

        $this->kernel->shouldReceive('handle')->with(m::any())->once()->andReturn($response);
        $this->kernel->shouldReceive('terminate')->with(m::any(), $response)->once();
        $this->kernel->shouldReceive('shutdown')->once();

        $this->writer->shouldReceive('write')->with('/index.html', 'Foobar!')->once();

        $this->renderer->render($route);
    }

    /**
     * Tests the render() method.
     *
     * The render() method creates a request based on the given route and lets the kernel handle the request. The
     * response is saved to disk. A generator is used to generate multiple responses.
     *
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::render()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::renderWithParameters()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::buildRequest()
     */
    public function renderShouldRenderRouteWithGenerator()
    {
        $generator = m::mock('Cocur\Bundle\BuildBundle\Generator\GeneratorInterface');
        $generator->shouldReceive('generate')->once()->andReturn([ [ 'var' => 'foo' ], [ 'var' => 'bar' ] ]);

        $this->generatorCollection->shouldReceive('has')->with('index')->once()->andReturn(true);
        $this->generatorCollection->shouldReceive('get')->with('index')->once()->andReturn($generator);

        $this->router->shouldReceive('generate')->with('index', [ 'var' => 'foo' ])->twice()->andReturn('/index/foo.html');
        $this->router->shouldReceive('generate')->with('index', [ 'var' => 'bar' ])->twice()->andReturn('/index/bar.html');

        $route = m::mock('Symfony\Component\Routing\Route');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->twice()->andReturn('Foobar!');

        $this->kernel->shouldReceive('handle')->with(m::any())->twice()->andReturn($response);
        $this->kernel->shouldReceive('terminate')->with(m::any(), $response)->twice();
        $this->kernel->shouldReceive('shutdown')->twice();

        $this->writer->shouldReceive('write')->with('/index/foo.html', 'Foobar!')->once();
        $this->writer->shouldReceive('write')->with('/index/bar.html', 'Foobar!')->once();

        $this->renderer->render($route, 'index');
    }

    /**
     * Tests the renderByName() method.
     *
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::renderByName()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::render()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::renderWithParameters()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::buildRequest()
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::getRoute()
     */
    public function renderByNameShouldRenderRouteByName()
    {
        $this->generatorCollection->shouldReceive('has')->with('index_route')->once()->andReturn(false);

        $this->router->shouldReceive('generate')->with('index_route', [])->twice()->andReturn('/index.html');

        $route = m::mock('Symfony\Component\Routing\Route');

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
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Renderer\RouteRenderer::renderByName()
     *
     * @expectedException Cocur\Bundle\BuildBundle\Exception\RouteNotFoundException
     */
    public function renderByNameShouldThrowExceptionIfRouteNotFound()
    {
        $route = m::mock('Symfony\Component\Routing\Route');

        $routeCollection = m::mock('Symfony\Component\Routing\RouteCollection');
        $routeCollection->shouldReceive('get')->with('invalid_route')->once()->andReturn(null);

        $this->router->shouldReceive('getRouteCollection')->once()->andReturn($routeCollection);

        $this->renderer->renderByName('invalid_route');
    }
}
