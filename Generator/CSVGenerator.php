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
use Braincrafted\Json\Json;

/**
 * CSVGenerator
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
class CSVGenerator implements GeneratorInterface
{
    /** @var array */
    private $default = [
        'delimiter' => ',',
        'enclosure' => '"',
        'escape'    => '\\'
    ];

    /** @var string */
    private $filename;

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

        $options = array_merge($this->default, $options);
        $this->filename  = $options['filename'];
        $this->delimiter = $options['delimiter'];
        $this->enclosure = $options['enclosure'];
        $this->escape    = $options['escape'];
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
     * Returns the delimiter character.
     *
     * @return string Delimiter character.
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Returns the enclosure character.
     *
     * @return string Enclosure character.
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * Returns the escape character.
     *
     * @return string Escape character.
     */
    public function getEscape()
    {
        return $this->escape;
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

        $header = $this->getCSV($handle);
        $parameters = [];
        while ($values = $this->getCSV($handle)) {
            $parameter = [];
            foreach ($values as $index => $value) {
                $parameter[$header[$index]] = $value;
            }
            $parameters[] = $parameter;
        }

        return $parameters;
    }

    /**
     * Returns the elements of a CSV line.
     *
     * @param resource $handle File handle.
     *
     * @return string[] Array of columns.
     */
    protected function getCSV($handle)
    {
        return fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape);
    }
}
