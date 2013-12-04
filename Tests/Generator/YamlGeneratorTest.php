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

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Yaml\Yaml;

use Braincrafted\Bundle\StaticSiteBundle\Generator\YamlGenerator;

/**
 * YamlGeneratorTest
 *
 * @category   Test
 * @package    BraincraftedStaticSiteBundle
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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\YamlGenerator::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\YamlGenerator::getFilename()
     */
    public function constructorShouldSetFilename()
    {
        $generator = new YamlGenerator([ 'filename' => 'file.yml' ]);
        $this->assertEquals('file.yml', $generator->getFilename());
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\YamlGenerator::__construct()
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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\YamlGenerator::generate()
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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\YamlGenerator::generate()
     *
     * @expectedException Braincrafted\Bundle\StaticSiteBundle\Exception\FileNotFoundException
     */
    public function generateShouldThrowExceptionIfFileNotFound()
    {
        $generator = new YamlGenerator([ 'filename' => vfsStream::url('data/parameters.yml') ]);

        $generator->generate();
    }
}
