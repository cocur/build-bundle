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

use Braincrafted\Bundle\StaticSiteBundle\Exception\FileNotFoundException;

/**
 * DirectoryGenerator.
 *
 * Generates parameters based on the filenames in a diretory.
 *
 * **Required parameters:**
 *
 * - `filename`
 * - `parameter`
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
class DirectoryGenerator implements GeneratorInterface
{
    /** @var string */
    private $directoryName;

    /** @var string */
    private $parameter;

    /**
     * Constructor.
     *
     * @param array $options Options array
     *
     * @throws \InvalidArgumentException if the option `directory_name` is missing.
     * @throws \InvalidArgumentException if the option `parameter` is missing.
     */
    public function __construct(array $options = array())
    {
        if (false === isset($options['directory_name'])) {
            throw new \InvalidArgumentException('The option "directory_name" must be set for a DirectoryGenerator.');
        }
        if (false === isset($options['parameter'])) {
            throw new \InvalidArgumentException('The option "parameter" must be set for a DirectoryGenerator.');
        }

        $this->directoryName  = $options['directory_name'];
        $this->parameter = $options['parameter'];
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
     * Returns the parameter.
     *
     * @return string Name of the parameter defined in the directory.
     */
    public function getParameter()
    {
        return $this->parameter;
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

        $finder = (new Finder())->in($this->directoryName)->depth('< 1');
        foreach ($finder as $file) {
            $parameters[] = [ $this->parameter => $file->getBasename(sprintf('.%s', $file->getExtension())) ];
        }

        return $parameters;
    }
}
