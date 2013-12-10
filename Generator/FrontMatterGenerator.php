<?php

/**
 * This file is part of CocurBuildBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Cocur\Bundle\BuildBundle\Generator;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

use Cocur\Bundle\BuildBundle\Exception\FileNotFoundException;

/**
 * FrontMatterGenerator.
 *
 * Generates parameters based on the front-matter of each file in a directory.
 *
 * **Required options:**
 *
 * - `filename`
 *
 * **Optional options:**
 *
 * - `parameters`
 *
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
class FrontMatterGenerator implements GeneratorInterface
{
    /** @var array */
    private $default = [
        'parameters' => null
    ];

    /** @var string */
    private $directoryName;

    /** @var array */
    private $parameters;

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

        $options = array_merge($this->default, $options);
        $this->directoryName = $options['directory_name'];
        $this->parameters    = $options['parameters'];
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
     * Returns the list of parameters.
     *
     * @return string[] List of parameters.
     */
    public function getParameters()
    {
        return $this->parameters;
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
            $yaml = Yaml::parse($this->getFrontMatter($file));
            $parameter = [];
            foreach ($yaml as $key => $value) {
                if (null === $this->parameters || true === in_array($key, $this->parameters)) {
                    $parameter[$key] = $value;
                }
            }
            $parameters[] = $parameter;
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
