<?php

/**
 * This file is part of BraincraftedCocurBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\CocurBundle\Tests\Generator;

use \Mockery as m;
use Braincrafted\Bundle\CocurBundle\Generator\GeneratorCollection;

/**
 * GeneratorCollectionTest
 *
 * @category   Test
 * @package    BraincraftedCocurBundle
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
     * @covers Braincrafted\Bundle\CocurBundle\Generator\GeneratorCollection::add()
     * @covers Braincrafted\Bundle\CocurBundle\Generator\GeneratorCollection::get()
     * @covers Braincrafted\Bundle\CocurBundle\Generator\GeneratorCollection::has()
     */
    public function addShouldAddGeneratorToCollection()
    {
        $generator = m::mock('Braincrafted\Bundle\CocurBundle\Generator\GeneratorInterface');
        $this->collection->add($generator, 'foo');
        $this->assertTrue($this->collection->has('foo'));
        $this->assertEquals($generator, $this->collection->get('foo'));
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\CocurBundle\Generator\GeneratorCollection::get()
     */
    public function getShouldReturnNullIfGeneratorNotFound()
    {
        $this->assertNull($this->collection->get('invalid'));
    }
}
