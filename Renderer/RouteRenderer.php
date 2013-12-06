<?php

/**
 * This file is part of BraincraftedCocurBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\CocurBundle\Renderer;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Router;

use Braincrafted\Bundle\CocurBundle\Exception\RouteNotFoundException;
use Braincrafted\Bundle\CocurBundle\Writer\WriterInterface;
use Braincrafted\Bundle\CocurBundle\Generator\GeneratorCollection;

/**
 * RouteRenderer renders a page based on the given route.
 *
 * @package    BraincraftedCocurBundle
 * @subpackage Renderer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class RouteRenderer
{
    /** @var Kernel */
    private $kernel;

    /** @var Router */
    private $router;

    /** @var WriterInterface */
    private $writer;

    /** @var GeneratorCollection */
    private $generatorCollection;

    /** @var string */
    private $baseUrl;

    /**
     * Constructor.
     *
     * @param Kernel              $kernel
     * @param Router              $router
     * @param WriterInterface     $writer
     * @param GeneratorCollection $generatorCollection
     * @param string              $baseUrl
     *
     * @codeCoverageIgnore
     */
    public function __construct(
        Kernel $kernel,
        Router $router,
        WriterInterface $writer,
        GeneratorCollection $generatorCollection,
        $baseUrl = null
    ) {
        $this->kernel              = $kernel;
        $this->router              = $router;
        $this->writer              = $writer;
        $this->generatorCollection = $generatorCollection;

        $this->setBaseUrl($baseUrl);
    }

    /**
     * Sets the base URL.
     *
     * @param string $baseUrl Base URL.
     *
     * @return RouteRenderer
     */
    public function setBaseUrl($baseUrl)
    {
        $this->baseUrl = '/'.trim(trim($baseUrl), '/');
        $this->router->getContext()->setBaseUrl($this->baseUrl);

        return $this;
    }

    /**
     * Returns the base URL.
     *
     * @return string Base URL.
     */
    public function getBaseUrl()
    {
        return $this->baseUrl;
    }

    /**
     * Renders the page with the route that matches the given name.
     *
     * @param string $name Name of a route
     *
     * @return void
     */
    public function renderByName($name)
    {
        $route = $this->getRoute($name);
        if (null === $route) {
            throw new RouteNotFoundException(sprintf('There is no route "%s".', $name));
        }

        $this->render($route, $name);
    }

    /**
     * Renders the page with the given route.
     *
     * @param Route  $route Route object.
     * @param string $name  Name of the route; the default value is `null`.
     *
     * @return void
     */
    public function render(Route $route, $name = null)
    {
        if (null !== $name && true === $this->generatorCollection->has($name)) {
            $parameters = $this->generatorCollection->get($name)->generate();
        } else {
            $parameters = [ [] ];
        }

        foreach ($parameters as $parameter) {
            $this->renderWithParameters($route, $name, $parameter);
        }
    }

    /**
     * Renders the given route with the given parameter.
     *
     * @param Route  $route     Route.
     * @param string $name      Name of the route; the default value is `null`.
     * @param array  $parameter Array of parameters; the default value is an empty array.
     *
     * @return void
     */
    protected function renderWithParameters(Route $route, $name = null, array $parameter = array())
    {
        $request = $this->buildRequest($route, $name, $parameter);

        $response = $this->kernel->handle($request);
        $content = $response->getContent();
        $this->kernel->terminate($request, $response);
        $this->kernel->shutdown();

        if (null !== $name) {
            $filename = $this->router->generate($name, $parameter);
        } else {
            $filename = $route->getPath();
        }

        $this->writer->write($filename, $content);
    }

    /**
     * Returns the route that matches the given route name.
     *
     * @param string $route Name of the route
     *
     * @return Route
     */
    protected function getRoute($name)
    {
        return $this->router->getRouteCollection()->get($name);
    }

    /**
     * Builds a new request object based on the given route.
     *
     * @param Route  $route      Route.
     * @param string $name       Name of the route.
     * @param array  $parameters Parameters of the request
     *
     * @return Request
     */
    protected function buildRequest(Route $route, $name = null, array $parameters = array())
    {
        if (null !== $name) {
            $uri = $this->router->generate($name, $parameters);
        } else {
            $uri = $route->getPath();
        }

        return new Request(
            [], // GET
            [], // POST
            [], // Attributes
            [], // Cookies
            [], // Files
            [
                'REQUEST_URI'     => $uri,
                'DOCUMENT_URI'    => $uri,
                'SCRIPT_FILENAME' => realpath($this->kernel->getRootDir().'/../web').$this->baseUrl.'/app.php',
                'SCRIPT_NAME'     => $this->baseUrl.'/app.php'
            ], // Server
            null // Content
        );
    }
}
