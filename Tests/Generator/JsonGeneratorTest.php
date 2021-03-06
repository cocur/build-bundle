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

use Cocur\Bundle\BuildBundle\Generator\JsonGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * JsonGeneratorTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class JsonGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var rg\bovigo\vfs\vfsStreamFile */
    private $file;

    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('data'));
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::__construct()
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::getFilename()
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::getParameters()
     */
    public function constructorShouldSetFilename()
    {
        $generator = new JsonGenerator([ 'filename' => 'file.json', 'parameters' => [ 'foo' ] ]);
        $this->assertEquals('file.json', $generator->getFilename());
        $this->assertEquals([ 'foo' ], $generator->getParameters());
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoFilename()
    {
        new JsonGenerator([ ]);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::generate()
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::filterByKey()
     */
    public function generateShouldReturnListOfParameters()
    {
        $file = vfsStream::newFile('parameters.json')->at(vfsStreamWrapper::getRoot());
        $generator = new JsonGenerator([ 'filename' => vfsStream::url('data/parameters.json') ]);
        $file->setContent(json_encode([
            [ 'a' => 'param1a', 'b' => 'param1b' ],
            [ 'a' => 'param2a', 'b' => 'param2b' ]
        ]));

        $parameters = $generator->generate();

        $this->assertCount(2, $parameters);
        $this->assertEquals('param1a', $parameters[0]['a']);
        $this->assertEquals('param1b', $parameters[0]['b']);
        $this->assertEquals('param2a', $parameters[1]['a']);
        $this->assertEquals('param2b', $parameters[1]['b']);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::generate()
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::filterByKey()
     */
    public function generateShouldReturnListOfParametersThatMatchParameters()
    {
        $file = vfsStream::newFile('parameters.json')->at(vfsStreamWrapper::getRoot());
        $generator = new JsonGenerator(
            [ 'filename' => vfsStream::url('data/parameters.json'), 'parameters' => [ 'a' ] ]
        );
        $file->setContent(json_encode([
            [ 'a' => 'param1a', 'b' => 'param1b' ],
            [ 'a' => 'param2a', 'b' => 'param2b' ]
        ]));

        $parameters = $generator->generate();

        $this->assertCount(2, $parameters);
        $this->assertCount(1, $parameters[0]);
        $this->assertCount(1, $parameters[1]);
        $this->assertEquals('param1a', $parameters[0]['a']);
        $this->assertEquals('param2a', $parameters[1]['a']);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\JsonGenerator::generate()
     *
     * @expectedException Cocur\Bundle\BuildBundle\Exception\FileNotFoundException
     */
    public function generateShouldThrowExceptionIfFileNotFound()
    {
        $generator = new JsonGenerator([ 'filename' => vfsStream::url('data/parameters.json') ]);

        $generator->generate();
    }
}
