<?php

/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\Generator;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

use Braincrafted\Bundle\StaticSiteBundle\Exception\FileNotFoundException;

/**
 * FrontMatterGenerator.
 *
 * Generates parameters based on the front-matter of each file in a directory.
 *
 * **Required parameters:**
 *
 * - `filename`
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
class FrontMatterGenerator implements GeneratorInterface
{
    /** @var string */
    private $directoryName;

    /**
     * Constructor.
     *
     * @param array $options Options array
     *
     * @throws \InvalidArgumentException if the option `directory_name` is missing.
     */
    public function __construct(array $options = array())
    {
        if (false === isset($options['directory_name'])) {
            throw new \InvalidArgumentException('The option "directory_name" must be set for a FrontMatterGenerator.');
        }

        $this->directoryName = $options['directory_name'];
    }

    /**
     * Returns the name of the directory.
     *
     * @return string Name of the directory.
     */
    public function getDirectoryName()
    {
        return $this->directoryName;
    }

    /**
     * {@inheritDoc}
     *
     * @throws FileNotFoundException if the directory does not exist.
     */
    public function generate()
    {
        if (false === file_exists($this->directoryName) || false === is_dir($this->directoryName)) {
            throw new FileNotFoundException(sprintf('The directory "%s" does not exist.', $this->directoryName));
        }

        $finder = (new Finder())->files()->in($this->directoryName)->depth('< 1');
        $parameters = [];

        foreach ($finder as $file) {
            $yaml = $this->getFrontMatter($file);
            $parameters[] = Yaml::parse($yaml);
        }

        return $parameters;
    }

    /**
     * Returns the front matter from the given file.
     *
     * @param \SplFileInfo $file File
     *
     * @return string YAML containing the front matter of the file.
     */
    protected function getFrontMatter(\SplFileInfo $file)
    {
        $yaml = null;
        $lines = file($file);
        if (true === isset($lines[0]) && '---' === trim($lines[0])) {
            for ($i = 1; $i < count($lines) && '---' !== trim($lines[$i]); $i++) {
                $yaml .= $lines[$i];
            }
        }

        return $yaml;
    }
}
