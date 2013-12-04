<?php

namespace Braincrafted\Bundle\StaticSiteBundle\Tests\Writer;

use \Mockery as m;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamWrapper;
use org\bovigo\vfs\vfsStreamDirectory;
use Symfony\Component\Filesystem\Filesystem;

use Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter;

/**
 * FilesystemWriterTest
 *
 * @group unit
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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter::write()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter::getRealName()
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
     * @covers Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter::write()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter::getRealName()
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
