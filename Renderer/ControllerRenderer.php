<?php
/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Renderer;

use Symfony\Bundle\FrameworkBundle\Controller\ControllerNameParser;
use Symfony\Component\Routing\Router;
use Braincrafted\Bundle\StaticSiteBundle\Exception\RouteNotFoundException;
use Braincrafted\Bundle\StaticSiteBundle\Exception\ControllerNotFoundException;

/**
 * ControllerRenderer renders a page based on the given name of a controller.
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Renderer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class ControllerRenderer
{
    /** @var Router */
    private $router;

    /** @var ControllerNameParser */
    private $nameParser;

    /** @var RouteRenderer */
    private $routeRenderer;

    /**
     * Constructor.
     *
     * @param Kernel               $kernel
     * @param ControllerNameParser $nameParser
     * @param Router               $router
     * @param string               $buildDirectory
     *
     * @codeCoverageIgnore
     */
    public function __construct(RouteRenderer $routeRenderer, ControllerNameParser $nameParser, Router $router)
    {
        $this->routeRenderer = $routeRenderer;
        $this->router = $router;
        $this->nameParser = $nameParser;
    }

    /*
     * Sets the base URL.
     * 
     * @param string $baseUrl Base URL.
     * 
     * @return ControllerRenderer
     */
    public function setBaseUrl($baseUrl)
    {
        $this->routeRenderer->setBaseUrl($baseUrl);

        return $this;
    }

    /**
     * Renders the page with the given controller name.
     *
     * @param string $controllerName Name of the controller
     */
    public function render($controllerName)
    {
        $controller = $this->getControllerName($controllerName);
        if (null === $controller) {
            throw new ControllerNotFoundException(sprintf('Could not find controller "%s".', $controllerName));
        }

        $route = $this->getRoute($controller);
        if (null === $route) {
            throw new RouteNotFoundException(sprintf('Could not find route for controller "%s".', $controllerName));
        }

        $this->routeRenderer->render($route);
    }

    /**
     * Returns the fully qualified name of the controller.
     *
     * @param string $controller Name of the controller
     *
     * @return string Fully qualified name of the controller
     */
    protected function getControllerName($controller)
    {
        try {
            return $this->nameParser->parse($controller);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Returns the route that matches the given controller name.
     *
     * @param string $controller Name of the controller
     *
     * @return Route
     */
    protected function getRoute($controller)
    {
        $routes = $this->router->getRouteCollection()->all();
        foreach ($routes as $route) {
            if ($controller === $route->getDefault('_controller')) {
                return $route;
            }
        }

        return null;
    }
}
