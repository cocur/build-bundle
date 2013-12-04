<?php

namespace Braincrafted\Bundle\StaticSiteBundle\Tests\Writer;

use \Mockery as m;

use Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter;
use Symfony\Component\Filesystem\Filesystem;

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
        $this->filesystem = new Filesystem;
        $this->buildDirectory = __DIR__.'/build';

        $this->filesystem->mkdir($this->buildDirectory);

        $this->writer = new FilesystemWriter($this->filesystem, $this->buildDirectory, 'index.html');
    }

    public function tearDown()
    {
        $this->filesystem->remove($this->buildDirectory);
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter::write()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter::getRealName()
     */
    public function writeShouldWriteFileToDisk()
    {
        $this->writer->write('/index.html', 'Foobar');

        $this->assertEquals('Foobar', file_get_contents($this->buildDirectory.'/index.html'));
    }

    /**
     * @test
     *
     * @covers Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter::write()
     * @covers Braincrafted\Bundle\StaticSiteBundle\Writer\FilesystemWriter::getRealName()
     */
    public function writeShouldWriteIfFileIsInDirectory()
    {
        $this->writer->write('/foo', 'Foobar');

        $this->assertEquals('Foobar', file_get_contents($this->buildDirectory.'/foo/index.html'));
    }
}
