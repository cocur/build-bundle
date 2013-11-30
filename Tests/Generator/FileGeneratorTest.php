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

use Braincrafted\Bundle\StaticSiteBundle\Generator\FileGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * FileGeneratorTest
 *
 * @category   Test
 * @package    BraincraftedStaticSiteBundle
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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\FileGenerator::__construct()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\FileGenerator::getFilename()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\FileGenerator::getParameter()
     */
    public function testGetFilenameGetParameterConstruct()
    {
        $generator = new FileGenerator('file.txt', 'name');
        $this->assertEquals('file.txt', $generator->getFilename());
        $this->assertEquals('name', $generator->getParameter());
    }

    /**
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\FileGenerator::generate()
     */
    public function testGenerate()
    {
        $file = vfsStream::newFile('parameters.txt')->at(vfsStreamWrapper::getRoot());
        $generator = new FileGenerator(vfsStream::url('data/parameters.txt'), 'var');
        $file->setContent("param1\nparam2\nparam3\n");

        $parameters = $generator->generate();

        $this->assertCount(3, $parameters);
        $this->assertEquals('param1', $parameters[0]['var']);
        $this->assertEquals('param2', $parameters[1]['var']);
        $this->assertEquals('param3', $parameters[2]['var']);
    }

    /**
     * @covers Braincrafted\Bundle\StaticSiteBundle\Generator\FileGenerator::generate()
     * @expectedException Braincrafted\Bundle\StaticSiteBundle\Exception\FileNotFoundException
     */
    public function testGenerateFileNotFound()
    {
        $generator = new FileGenerator(vfsStream::url('data/parameters.txt'), 'var');

        $generator->generate();
    }
}
