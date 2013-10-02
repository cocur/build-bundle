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

use Symfony\Component\Routing\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Kernel;

/**
 * RouteRenderer renders a page based on the given route.
 *
 * @package    BcStaticSiteBundle
 * @subpackage Renderer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class RouteRenderer
{
    /** @var Kernel */
    private $kernel;

    /** @var string */
    private $buildDirectory;

    /**
     * Constructor.
     *
     * @param Kernel $kernel
     * @param string $buildDirectory
     *
     * @codeCoverageIgnore
     */
    public function __construct(Kernel $kernel, $buildDirectory)
    {
        $this->kernel = $kernel;
        $this->buildDirectory = $buildDirectory;
    }

    /**
     * Renders the page with the given route.
     *
     * @param Route $route
     */
    public function render(Route $route)
    {
        $request = $this->buildRequest($route);

        $response = $this->kernel->handle($request);
        $content = $response->getContent();
        $this->kernel->terminate($request, $response);
        $this->kernel->shutdown();

        file_put_contents(sprintf('%s/%s', $this->buildDirectory, $route->getPattern()), $content);
    }

    /**
     * Builds a new request object based on the given route.
     *
     * @param Route $route
     *
     * @return Request
     */
    protected function buildRequest(Route $route)
    {
        return new Request(
            [], // GET
            [], // POST
            [], // Attributes
            [], // Cookies
            [], // Files
            [
                'REQUEST_URI' => $route->getPattern(),
                'DOCUMENT_URI' => $route->getPattern()
            ], // Server
            null // Content
        );
    }
}
