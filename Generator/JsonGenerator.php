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
 * JsonGenerator.
 *
 * Generates parameters based on a JSON file.
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
class JsonGenerator implements GeneratorInterface
{
    /** @var array */
    private $default = [
        'parameters' => null
    ];

    /** @var string */
    private $filename;

    /** @var array */
    private $parameters;

    /**
     * Constructor.
     *
     * @param array $options Options array.
     *
     * @throws \InvalidArgumentException if the option `filename` is missing.
     */
    public function __construct(array $options = array())
    {
        if (false === isset($options['filename'])) {
            throw new \InvalidArgumentException('The option "filename" must be set for a JsonGenerator.');
        }

        $options = array_merge($this->default, $options);
        $this->filename   = $options['filename'];
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

        $json = Json::decode(file_get_contents($this->filename), true);

        for ($i = 0; $i < count($json); $i++) {
            $json[$i] = $this->filterByKey($json[$i], function ($key, $value) {
                return null === $this->parameters || true === in_array($key, $this->parameters);
            });
        }

        return array_filter($json, function ($value) {
            return count($value) > 0;
        });
    }

    /**
     * @param array    $array
     * @param callable $callback
     *
     * @return array
     */
    protected function filterByKey(array $array, callable $callback)
    {
        $newArray = [];
        foreach ($array as $key => $value) {
            if (true === $callback($key, $value)) {
                $newArray[$key] = $value;
            }
        }

        return $newArray;
    }
}
