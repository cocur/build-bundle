<?php
/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Writer;

/**
 * Interfaces for classes that write pages.
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Writer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
interface WriterInterface
{
    /**
     * Writes the given content into a file with the given name.
     *
     * @param string $name    Name of the file
     * @param string $content Content of the file
     */
    public function write($name, $content);
}
