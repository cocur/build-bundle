<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Tests\Generator;

use \Mockery as m;
use Cocur\Bundle\BuildBundle\Generator\GeneratorCollection;

/**
 * GeneratorCollectionTest
 *
 * @category   Test
 * @package    CocurBuildBundle
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
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\GeneratorCollection::add()
     * @covers Cocur\Bundle\BuildBundle\Generator\GeneratorCollection::get()
     * @covers Cocur\Bundle\BuildBundle\Generator\GeneratorCollection::has()
     */
    public function addShouldAddGeneratorToCollection()
    {
        $generator = m::mock('Cocur\Bundle\BuildBundle\Generator\GeneratorInterface');
        $this->collection->add($generator, 'foo');
        $this->assertTrue($this->collection->has('foo'));
        $this->assertEquals($generator, $this->collection->get('foo'));
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\GeneratorCollection::get()
     */
    public function getShouldReturnNullIfGeneratorNotFound()
    {
        $this->assertNull($this->collection->get('invalid'));
    }
}
