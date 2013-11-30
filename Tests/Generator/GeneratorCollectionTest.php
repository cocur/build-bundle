<?php

/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Tests\Generator;

use \Mockery as m;
use Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorCollection;

/**
 * GeneratorCollectionTest
 *
 * @category   Test
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class GeneratorCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->collection = new GeneratorCollection();
    }

    /**
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorCollection::add()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorCollection::get()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorCollection::has()
     */
    public function testAddHasGet()
    {
        $generator = m::mock('Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorInterface');
        $this->collection->add($generator, '/foo/{var}');
        $this->assertTrue($this->collection->has('/foo/{var}'));
        $this->assertEquals($generator, $this->collection->get('/foo/{var}'));
    }

    /**
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\GeneratorCollection::get()
     */
    public function testGetNoGenerator()
    {
        $this->assertNull($this->collection->get('/invalid'));
    }
}
