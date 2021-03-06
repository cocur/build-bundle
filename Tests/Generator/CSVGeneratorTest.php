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

use Cocur\Bundle\BuildBundle\Generator\CSVGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * CSVGeneratorTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class CSVGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::__construct()
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::getFilename()
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::getDelimiter()
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::getEnclosure()
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::getEscape()
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::getParameters()
     */
    public function constructorShouldSetFilename()
    {
        $generator = new CSVGenerator([
            'filename'   => 'file.csv',
            'delimiter'  => ';',
            'enclosure'  => '\'',
            'escape'     => '\\',
            'parameters' => [ 'foo' ]
        ]);

        $this->assertEquals('file.csv', $generator->getFilename());
        $this->assertEquals(';', $generator->getDelimiter());
        $this->assertEquals('\'', $generator->getEnclosure());
        $this->assertEquals('\\', $generator->getEscape());
        $this->assertEquals([ 'foo' ], $generator->getParameters());
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoFilename()
    {
        new CSVGenerator([ ]);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::generate()
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::getCsv()
     */
    public function generateShouldReturnListOfParameters()
    {
        $file = vfsStream::newFile('parameters.csv')->at(vfsStreamWrapper::getRoot());
        $generator = new CSVGenerator([ 'filename' => vfsStream::url('data/parameters.csv') ]);
        $file->setContent("\"a\",\"b\"\n\"param1a\",\"param1b\"\n\"param2a\",\"param2b\"\n");

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
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::generate()
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::getCsv()
     */
    public function generateShouldReturnListOfParametersThatMatchParameters()
    {
        $file = vfsStream::newFile('parameters.csv')->at(vfsStreamWrapper::getRoot());
        $generator = new CSVGenerator(
            [ 'filename' => vfsStream::url('data/parameters.csv'), 'parameters' => [ 'a' ] ]
        );
        $file->setContent("\"a\",\"b\"\n\"param1a\",\"param1b\"\n\"param2a\",\"param2b\"\n");

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
     * @covers Cocur\Bundle\BuildBundle\Generator\CSVGenerator::generate()
     *
     * @expectedException Cocur\Bundle\BuildBundle\Exception\FileNotFoundException
     */
    public function generateShouldThrowExceptionIfFileNotFound()
    {
        $generator = new CSVGenerator([ 'filename' => vfsStream::url('data/parameters.csv') ]);

        $generator->generate();
    }
}
