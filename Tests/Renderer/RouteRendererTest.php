<?php

/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Tests\Renderer;

use \Mockery as m;

use Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer;

/**
 * RouteRendererTest
 *
 * @category   Test
 * @package    BraincraftedStaticSiteBundle
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

    /** @var Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorCollection */
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
        $this->writer = m::mock('Braincrafted\Bundle\StaticSiteBundle\Writer\WriterInterface');
        $this->generatorCollection = m::mock('Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorCollection');

        $this->renderer = new RouteRenderer($this->kernel, $this->router, $this->writer, $this->generatorCollection);
    }

    /**
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::setBaseUrl()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::getBaseUrl()
     */
    public function testSetBaseUrlGetBaseUrl()
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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::render()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::renderWithParameters()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::buildRequest()
     */
    public function testRender()
    {
        $this->generatorCollection->shouldReceive('has')->once()->andReturn(false);
        $this->router->shouldReceive('generate')->with('index', [])->twice()->andReturn('/index.html');

        $route = m::mock('Symfony\Component\Routing\Route');
        $route->shouldReceive('setPath')->once();
        $route->shouldReceive('getPath')->times(2)->andReturn('/index.html');

        $response = m::mock('Symfony\Component\HttpFoundation\Response');
        $response->shouldReceive('getContent')->once()->andReturn('Foobar!');

        $this->kernel->shouldReceive('handle')->with(m::any())->once()->andReturn($response);
        $this->kernel->shouldReceive('terminate')->with(m::any(), $response)->once();
        $this->kernel->shouldReceive('shutdown')->once();

        $this->writer->shouldReceive('write')->with('/index.html', 'Foobar!')->once();

        $this->renderer->render($route, 'index');
    }

    /**
     * Tests the render() method.
     *
     * The render() method creates a request based on the given route and lets the kernel handle the request. The
     * response is saved to disk. A generator is used to generate multiple responses.
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::render()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::renderWithParameters()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::buildRequest()
     */
    public function testRenderWithGenerator()
    {
        $generator = m::mock('Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorInterface');
        $generator->shouldReceive('generate')->once()->andReturn([ [ 'var' => 'foo' ], [ 'var' => 'bar' ] ]);

        $this->generatorCollection->shouldReceive('has')->with('/index/{var}.html')->once()->andReturn(true);
        $this->generatorCollection->shouldReceive('get')->with('/index/{var}.html')->once()->andReturn($generator);

        $this->router->shouldReceive('generate')->with('index', [ 'var' => 'foo' ])->twice()->andReturn('/index/foo.html');
        $this->router->shouldReceive('generate')->with('index', [ 'var' => 'bar' ])->twice()->andReturn('/index/bar.html');

        $route = m::mock('Symfony\Component\Routing\Route');
        $route->shouldReceive('setPath')->once();
        $route->shouldReceive('getPath')->times(3)->andReturn('/index/{var}.html');

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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::renderByName()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::render()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::renderWithParameters()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::buildRequest()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::getRoute()
     */
    public function testRenderByName()
    {
        $this->generatorCollection->shouldReceive('has')->with('/index.html')->once()->andReturn(false);

        $this->router->shouldReceive('generate')->with('index_route', [])->twice()->andReturn('/index.html');

        $route = m::mock('Symfony\Component\Routing\Route');
        $route->shouldReceive('setPath')->once();
        $route->shouldReceive('getPath')->twice()->andReturn('/index.html');

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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Renderer\RouteRenderer::renderByName()
     *
     * @expectedException Braincrafted\Bundle\StaticSiteBundle\Exception\RouteNotFoundException
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
