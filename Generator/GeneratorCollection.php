<?php

/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Generator;

/**
 * GeneratorCollection
 *
 * @package    BraincraftedStaticSiteBundle
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
     * Adds a genearator for the given path to the collection.
     * @param GeneratorInterface $generator Generator.
     * @param string             $path      Path to use the generator for.
     */
    public function add(GeneratorInterface $generator, $path)
    {
        $this->generators[$path] = $generator;

        return $this;
    }

    /**
     * Returns if there exists a generator for the given path.
     *
     * @param string $path Path.
     *
     * @return boolean `true` if a generator exists, `false` if not.
     */
    public function has($path)
    {
        return isset($this->generators[$path]);
    }

    /**
     * Returns the generator for the given path or `null` if no generator exists.
     *
     * @param string $path Path.
     *
     * @return GeneratorInterface Generator for the given path or `null` if no generator exists.
     */
    public function get($path)
    {
        if (false === $this->has($path)) {
            return null;
        }

        return $this->generators[$path];
    }
}
