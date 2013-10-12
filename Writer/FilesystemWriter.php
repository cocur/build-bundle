<?php
/**
 * This file is part of BcStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Bc\Bundle\StaticSiteBundle\Writer;

use Symfony\Component\Filesystem\Filesystem;

/**
 * FilesystemWriter writes pages to the filesystem.
 *
 * @package    BcStaticSiteBundle
 * @subpackage Writer
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 */
class FilesystemWriter implements WriterInterface
{
    /** @var Filesystem */
    private $filesystem;

    /** @var string */
    private $buildDirectory;

    /** @var string */
    private $indexName;

    /**
     * Constructor.
     *
     * @param string     $buildDirectory Build directory
     * @param Filesystem $filesystem     Filesystem
     *
     * @codeCoverageIgnore
     */
    public function __construct(Filesystem $filesystem, $buildDirectory, $indexName)
    {
        $this->filesystem     = $filesystem;
        $this->buildDirectory = $buildDirectory;
        $this->indexName      = $indexName;
    }

    /**
     * Writes the given file to the filesystem.
     *
     * @param string $name    Name of the file
     * @param string $content Content of the file
     */
    public function write($name, $content)
    {
        $name = $this->getRealName($name);
        $directory = sprintf('%s%s', $this->buildDirectory, substr($name, 0, strrpos($name, '/')));
        $name = substr($name, strrpos($name, '/'));

        if (false === file_exists($directory)) {
            $this->filesystem->mkdir($directory);
        }

        file_put_contents(sprintf('%s/%s', $directory, $name), $content);
    }

    /**
     * If $name does not contain an extension at the end it is considered a directory and the default
     * index name is added at the end.
     *
     * @param string $name Name of the page
     *
     * @return string Real name of the page
     */
    protected function getRealName($name)
    {
        $pattern = '/(html|htm|xhtml|php|js|css|xml|json)$/';
        if (0 === preg_match($pattern, $name)) {
            $name = preg_replace('/(\/)$/', '', $name).'/'.$this->indexName;
        }

        return $name;
    }
}
