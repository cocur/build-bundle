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
 * DirectoryGenerator
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
     * @param string $directoryName Name of the directory.
     * @param string $parameter     Name of the parameter defined in the file.
     */
    public function __construct(array $options = array())
    {
        if (false === isset($options['directoryName'])) {
            throw new \InvalidArgumentException('The option "directoryName" must be set for a DirectoryGenerator.');
        }
        if (false === isset($options['parameter'])) {
            throw new \InvalidArgumentException('The option "parameter" must be set for a DirectoryGenerator.');
        }

        $this->directoryName  = $options['directoryName'];
        $this->parameter = $options['parameter'];
    }

    /**
     * Returns the directoryName.
     *
     * @return string Filename.
     */
    public function getDirectoryName()
    {
        return $this->directoryName;
    }

    /**
     * Returns the parameter.
     *
     * @return string Name of the parameter defined in the file.
     */
    public function getParameter()
    {
        return $this->parameter;
    }

    /**
     * {@inheritDoc}
     *
     * @throws FileNotFoundException if the file does not exist.
     * @throws \RuntimeException if the file could not be opened.
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
