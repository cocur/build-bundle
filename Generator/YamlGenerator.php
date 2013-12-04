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

use Symfony\Component\Yaml\Yaml;

use Braincrafted\Bundle\StaticSiteBundle\Exception\FileNotFoundException;

/**
 * YamlGenerator.
 *
 * Generates parameters based on a YAML file. The YAML file should have a maximum depth of 1.
 *
 * **Required options:**
 * - `filename`
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage Generator
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
*/
class YamlGenerator implements GeneratorInterface
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
     * @param array $options Array with options.
     *
     * @throws \InvalidArgumentException if the option `filename` is not set.
     */
    public function __construct(array $options = array())
    {
        if (false === isset($options['filename'])) {
            throw new \InvalidArgumentException('The option "filename" must be set for a YamlGenerator.');
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
     * @throws Symfony\Component\Yaml\Exception\ParseException if YAML is not valid
     */
    public function generate()
    {
        if (false === file_exists($this->filename) || false === is_file($this->filename)) {
            throw new FileNotFoundException(sprintf('The file "%s" does not exist.', $this->filename));
        }

        $yaml = Yaml::parse($this->filename);
        $parameters = [];

        for ($i = 0; $i < count($yaml); $i++) {
            $parameter = [];
            foreach ($yaml[$i] as $key => $value) {
                if (null === $this->parameters || true === in_array($key, $this->parameters)) {
                    $parameter[$key] = $value;
                }
            }
            $parameters[] = $parameter;
        }

        return $parameters;
    }
}
