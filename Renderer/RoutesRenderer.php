<?php
/**
 * This file is part of BcStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bc\Bundle\StaticSiteBundle\Renderer;

use Symfony\Component\Routing\Router;

/**
 * RoutesRenderer renders all routes into pages.
 *
 * @package    BcStaticSiteBundle
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

    /**
     * Constructor.
     *
     * @param RouteRenderer $routeRenderer Route renderer
     * @param Router        $router        Router
     *
     * @codeCoverageIgnore
     */
    public function __construct(RouteRenderer $routeRenderer, Router $router)
    {
        $this->routeRenderer = $routeRenderer;
        $this->router = $router;
    }

    /**
     * Renders all public routes, that is, routes that do not start with "_".
     *
     * @return integer Number of rendered routes
     */
    public function render()
    {
        $routes = $this->router->getRouteCollection()->all();
        $counter = 0;

        foreach ($routes as $name => $route) {
            if ('_' !== substr($name, 0, 1)) {
                $this->routeRenderer->render($route);
                $counter += 1;
            }
        }

        return $counter;
    }
}
