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

use Cocur\Bundle\BuildBundle\Exception\FileNotFoundException;
use Braincrafted\Json\Json;

/**
 * CSVGenerator.
 *
 * Generates parameters based on a CSV file.
 *
 * **Required parameters:**
 *
 * - `filename`
 *
 * **Optional parameters:**
 *
 * - `delimiter` (default value: `,`)
 * - `enclosure` (default value: `"`)
 * - `escape` (default value: `\`)
 * - `parameters` (default value: `null`)
 *
 * @package    CocurBuildBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
class CSVGenerator implements GeneratorInterface
{
    /** @var array */
    private $default = [
        'delimiter'  => ',',
        'enclosure'  => '"',
        'escape'     => '\\',
        'parameters' => null
    ];

    /** @var string */
    private $filename;

    /** @var string */
    private $delimiter;

    /** @var string */
    private $enclosure;

    /** @var string */
    private $escape;

    /** @var array */
    private $parameters;

    /**
     * Constructor.
     *
     * @param array $options Options array
     *
     * @throws \InvalidArgumentException when the option `filename` is missing.
     */
    public function __construct(array $options = array())
    {
        if (false === isset($options['filename'])) {
            throw new \InvalidArgumentException('The option "filename" must be set for a CSVGenerator.');
        }

        $options = array_merge($this->default, $options);
        $this->filename   = $options['filename'];
        $this->delimiter  = $options['delimiter'];
        $this->enclosure  = $options['enclosure'];
        $this->escape     = $options['escape'];
        $this->parameters = $options['parameters'];
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

        $header = $this->getCsv($handle);
        $parameters = [];
        while ($values = $this->getCsv($handle)) {
            $parameter = [];
            foreach ($values as $index => $value) {
                if (null === $this->parameters || true === in_array($header[$index], $this->parameters)) {
                    $parameter[$header[$index]] = $value;
                }
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
    protected function getCsv($handle)
    {
        return fgetcsv($handle, 0, $this->delimiter, $this->enclosure, $this->escape);
    }
}
