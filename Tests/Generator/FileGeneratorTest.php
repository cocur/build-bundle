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

use Cocur\Bundle\BuildBundle\Generator\FileGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * FileGeneratorTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class FileGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @covers Cocur\Bundle\BuildBundle\Generator\FileGenerator::__construct()
     * @covers Cocur\Bundle\BuildBundle\Generator\FileGenerator::getFilename()
     * @covers Cocur\Bundle\BuildBundle\Generator\FileGenerator::getParameter()
     */
    public function constructorShouldSetFilenameAndParameter()
    {
        $generator = new FileGenerator([ 'filename' => 'file.txt', 'parameter' => 'name' ]);
        $this->assertEquals('file.txt', $generator->getFilename());
        $this->assertEquals('name', $generator->getParameter());
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\FileGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoFilename()
    {
        new FileGenerator([ 'parameter' => 'name' ]);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\FileGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoParameter()
    {
        new FileGenerator([ 'filename' => 'file.txt' ]);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\FileGenerator::generate()
     */
    public function generateShouldReturnListOfParameters()
    {
        $file = vfsStream::newFile('parameters.txt')->at(vfsStreamWrapper::getRoot());
        $generator = new FileGenerator([ 'filename' => vfsStream::url('data/parameters.txt'), 'parameter' => 'var' ]);
        $file->setContent("param1\nparam2\nparam3\n");

        $parameters = $generator->generate();

        $this->assertCount(3, $parameters);
        $this->assertEquals('param1', $parameters[0]['var']);
        $this->assertEquals('param2', $parameters[1]['var']);
        $this->assertEquals('param3', $parameters[2]['var']);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\FileGenerator::generate()
     *
     * @expectedException Cocur\Bundle\BuildBundle\Exception\FileNotFoundException
     */
    public function generateShouldThrowExceptionIfFileNotFound()
    {
        $generator = new FileGenerator([ 'filename' => vfsStream::url('data/parameters.txt'), 'parameter' => 'var' ]);

        $generator->generate();
    }
}
