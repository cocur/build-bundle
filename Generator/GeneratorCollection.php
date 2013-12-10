<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Generator;

/**
 * GeneratorCollection
 *
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
class GeneratorCollection
{
    /** @var array */
    private $generators = [];

    /**
     * Adds a genearator for the given route to the collection.
     *
     * @param GeneratorInterface $generator Generator.
     * @param string             $route     Name of the route the generator is used for.
     * @param array              $options   Options
     *
     * @return GeneratorCollection
     */
    public function add(GeneratorInterface $generator, $route)
    {
        $this->generators[$route] = $generator;

        return $this;
    }

    /**
     * Returns if there exists a generator for the given route.
     *
     * @param string $route Name of a route.
     *
     * @return boolean `true` if a generator exists, `false` if not.
     */
    public function has($route)
    {
        return isset($this->generators[$route]);
    }

    /**
     * Returns the generator for the given route or `null` if no generator exists.
     *
     * @param string $route Name of a route.
     *
     * @return GeneratorInterface Generator for the given route or `null` if no generator exists.
     */
    public function get($route)
    {
        if (false === $this->has($route)) {
            return null;
        }

        return $this->generators[$route];
    }
}
