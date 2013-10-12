<?php

namespace Bc\Bundle\StaticSiteBundle\Writer;

class FilesystemWriter implements WriterInterface
{
    /** @var string */
    private $buildDirectory;

    /**
     * Constructor.
     *
     * @param string $buildDirectory Build directory
     *
     * @codeCoverageIgnore
     */
    public function __construct($buildDirectory)
    {
        $this->buildDirectory = $buildDirectory;
    }

    /**
     * Writes the given file to the filesystem.
     *
     * @param string $name    Name of the file
     * @param string $content Content of the file
     */
    public function write($name, $content)
    {
        file_put_contents(sprintf('%s/%s', $this->buildDirectory, $name), $content);
    }
}
