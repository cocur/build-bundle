<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Renderer;

use Symfony\Component\Routing\Router;

/**
 * RoutesRenderer renders all routes into pages.
 *
 * @package    CocurBuildBundle
 * @subpackage Renderer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class RoutesRenderer
{
    /** @var RouteRenderer */
    private $routeRenderer;

    /** @var Router */
    private $router;

    /** @var string[] */
    private $routes;

    /**
     * Constructor.
     *
     * @param RouteRenderer $routeRenderer Route renderer
     * @param Router        $router        Router
     * @param string[]      $routes        Array of routes that should be rendered
     *
     * @codeCoverageIgnore
     */
    public function __construct(RouteRenderer $routeRenderer, Router $router, array $routes = array())
    {
        $this->routeRenderer = $routeRenderer;
        $this->router        = $router;
        $this->routes        = $routes;
    }

    /**
     * Sets the base URL.
     *
     * @param string $baseUrl Base URL for rendering the routes.
     *
     * @return RoutesRenderer
     */
    public function setBaseUrl($baseUrl)
    {
        $this->routeRenderer->setBaseUrl($baseUrl);

        return $this;
    }

    /**
     * Renders all public routes, that is, routes that do not start with "_".
     *
     * @return integer Number of rendered routes
     */
    public function render()
    {
        $counter = 0;

        foreach ($this->getRoutes() as $name => $route) {
            if ('_' !== substr($name, 0, 1)) {
                $this->routeRenderer->render($route, $name);
                $counter += 1;
            }
        }

        return $counter;
    }

    /**
     * Returns the list of routes that should be rendered.
     *
     * @return Symfony\Component\Routing\Route[] List of routes that should be rendered.
     */
    protected function getRoutes()
    {
        if (0 === count($this->routes)) {
            return $this->router->getRouteCollection()->all();
        }

        $routes = [];
        foreach ($this->routes as $name) {
            $routes[$name] = $this->router->getRouteCollection()->get($name);
        }

        return $routes;
    }
}
