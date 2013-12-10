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

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Yaml\Yaml;

use Cocur\Bundle\BuildBundle\Generator\YamlGenerator;

/**
 * YamlGeneratorTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class YamlGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @covers Cocur\Bundle\BuildBundle\Generator\YamlGenerator::__construct()
     * @covers Cocur\Bundle\BuildBundle\Generator\YamlGenerator::getFilename()
     * @covers Cocur\Bundle\BuildBundle\Generator\YamlGenerator::getParameters()
     */
    public function constructorShouldSetFilename()
    {
        $generator = new YamlGenerator([ 'filename' => 'file.yml', 'parameters' => [ 'foo' ] ]);
        $this->assertEquals('file.yml', $generator->getFilename());
        $this->assertEquals([ 'foo' ], $generator->getParameters());
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\YamlGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoFilename()
    {
        new YamlGenerator([ ]);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\YamlGenerator::generate()
     */
    public function generateShouldReturnListOfParameters()
    {
        $file = vfsStream::newFile('parameters.yml')->at(vfsStreamWrapper::getRoot());
        $generator = new YamlGenerator([ 'filename' => vfsStream::url('data/parameters.yml') ]);
        $file->setContent(Yaml::dump([
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
     * @covers Cocur\Bundle\BuildBundle\Generator\YamlGenerator::generate()
     */
    public function generateShouldReturnListOfParametersThatMatch()
    {
        $file = vfsStream::newFile('parameters.yml')->at(vfsStreamWrapper::getRoot());
        $generator = new YamlGenerator(
            [ 'filename' => vfsStream::url('data/parameters.yml'), 'parameters' => [ 'a' ] ]
        );
        $file->setContent(Yaml::dump([
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
     * @covers Cocur\Bundle\BuildBundle\Generator\YamlGenerator::generate()
     *
     * @expectedException Cocur\Bundle\BuildBundle\Exception\FileNotFoundException
     */
    public function generateShouldThrowExceptionIfFileNotFound()
    {
        $generator = new YamlGenerator([ 'filename' => vfsStream::url('data/parameters.yml') ]);

        $generator->generate();
    }
}
