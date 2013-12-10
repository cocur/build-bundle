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
 * GeneratorInterface
 *
 * @package    CocurBuildBundle
 * @subpackage Exception
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
interface GeneratorInterface
{
    /**
     * Generates parameters.
     *
     * @return array List of parameters.
     */
    public function generate();
}
