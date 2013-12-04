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

use Braincrafted\Bundle\StaticSiteBundle\Exception\FileNotFoundException;

/**
 * FileGenerator
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
class FileGenerator implements GeneratorInterface
{
    /** @var string */
    private $filename;

    /** @var string */
    private $parameter;

    /**
     * Constructor.
     *
     * @param string $filename  Filename.
     * @param string $parameter Name of the parameter defined in the file.
     */
    public function __construct(array $options = array())
    {
        if (false === isset($options['filename'])) {
            throw new \InvalidArgumentException('The option "filename" must be set for a FileGenerator.');
        }
        if (false === isset($options['parameter'])) {
            throw new \InvalidArgumentException('The option "parameter" must be set for a FileGenerator.');
        }

        $this->filename  = $options['filename'];
        $this->parameter = $options['parameter'];
    }

    /**
     * Returns the filename.
     *
     * @return string Filename.
     */
    public function getFilename()
    {
        return $this->filename;
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
        if (false === file_exists($this->filename) || false === is_file($this->filename)) {
            throw new FileNotFoundException(sprintf('The file "%s" does not exist.', $this->filename));
        }

        $handle = fopen($this->filename, 'r');
        if (false === $handle) {
            throw new \RuntimeException(sprintf('Could not open file "%s".', $this->filename));
        }

        $parameters = [];
        while ($line = fgets($handle)) {
            $parameters[] = [ $this->parameter => trim($line) ];
        }

        return $parameters;
    }
}
