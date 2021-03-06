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

use Cocur\Bundle\BuildBundle\Generator\DirectoryGenerator;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;

/**
 * DirectoryGeneratorTest
 *
 * @category   Test
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class DirectoryGeneratorTest extends \PHPUnit_Framework_TestCase
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
     * @covers Cocur\Bundle\BuildBundle\Generator\DirectoryGenerator::__construct()
     * @covers Cocur\Bundle\BuildBundle\Generator\DirectoryGenerator::getDirectoryName()
     * @covers Cocur\Bundle\BuildBundle\Generator\DirectoryGenerator::getParameter()
     */
    public function constructorShouldSetFilenameAndParameter()
    {
        $generator = new DirectoryGenerator([ 'directory_name' => 'files', 'parameter' => 'name' ]);
        $this->assertEquals('files', $generator->getDirectoryName());
        $this->assertEquals('name', $generator->getParameter());
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\DirectoryGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoDirectoryName()
    {
        new DirectoryGenerator([ 'parameter' => 'name' ]);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\DirectoryGenerator::__construct()
     *
     * @expectedException \InvalidArgumentException
     */
    public function constructorShouldThrowExceptionIfNoParameter()
    {
        new DirectoryGenerator([ 'directory_name' => 'files' ]);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\DirectoryGenerator::generate()
     */
    public function generateShouldReturnListOfParameters()
    {
        $root = vfsStreamWrapper::getRoot();
        $dir = new vfsStreamDirectory('data');
        $root->addChild($dir);
        $dir->addChild(vfsStream::newFile('param1.txt'));
        $dir->addChild(vfsStream::newFile('param2.txt'));
        $dir->addChild(vfsStream::newFile('param3.txt'));

        $generator = new DirectoryGenerator([ 'directory_name' => $dir->url(), 'parameter' => 'var' ]);

        $parameters = $generator->generate();

        $this->assertCount(3, $parameters);
        $this->assertEquals('param1', $parameters[0]['var']);
        $this->assertEquals('param2', $parameters[1]['var']);
        $this->assertEquals('param3', $parameters[2]['var']);
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Generator\DirectoryGenerator::generate()
     *
     * @expectedException Cocur\Bundle\BuildBundle\Exception\FileNotFoundException
     */
    public function generateShouldThrowExceptionIfFileNotFound()
    {
        $generator = new DirectoryGenerator(
            ['directory_name' => vfsStream::url('data/invalid'), 'parameter' => 'var' ]
        );

        $generator->generate();
    }
}
