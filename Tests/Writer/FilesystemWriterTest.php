<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Tests\Writer;

use \Mockery as m;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Filesystem\Filesystem;

use Cocur\Bundle\BuildBundle\Writer\FilesystemWriter;

/**
 * FilesystemWriterTest
 *
 * @category   Tests
 * @package    CocurBuildBundle
 * @subpackage Writer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @group      unit
 */
class FilesystemWriterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Symfony\Component\Filesystem\Filesystem */
    private $filesystem;

    /** @var string */
    private $buildDirectory;

    public function setUp()
    {
        $buildDir = new vfsStreamDirectory('build');
        $this->buildDirectory = $buildDir->url();

        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot($buildDir);

        $this->filesystem = new Filesystem();
        $this->filesystem->mkdir($this->buildDirectory);

        $this->writer = new FilesystemWriter($this->filesystem, $this->buildDirectory, 'index.html');
    }

    public function tearDown()
    {
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Writer\FilesystemWriter::write()
     * @covers Cocur\Bundle\BuildBundle\Writer\FilesystemWriter::getRealName()
     */
    public function writeShouldWriteFileToDisk()
    {
        $root = vfsStreamWrapper::getRoot();
        $this->writer->write('/index.html', 'Foobar');

        $this->assertTrue($root->hasChild('index.html'));
        $this->assertEquals('Foobar', $root->getChild('index.html')->getContent());
    }

    /**
     * @test
     *
     * @covers Cocur\Bundle\BuildBundle\Writer\FilesystemWriter::write()
     * @covers Cocur\Bundle\BuildBundle\Writer\FilesystemWriter::getRealName()
     */
    public function writeShouldWriteIfFileIsInDirectory()
    {
        $root = vfsStreamWrapper::getRoot();

        $this->writer->write('/foo', 'Foobar');

        $this->assertTrue($root->hasChild('foo'));
        $this->assertTrue($root->getChild('foo')->hasChild('index.html'));
        $this->assertEquals('Foobar', $root->getChild('foo')->getChild('index.html')->getContent());
    }
}
