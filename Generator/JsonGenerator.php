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
 * JsonGenerator.
 *
 * Generates parameters based on a JSON file.
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
class JsonGenerator implements GeneratorInterface
{
    /** @var string */
    private $filename;

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

        $this->filename  = $options['filename'];
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

        $content = file_get_contents($this->filename);
        if (false === $content) {
            throw new \RuntimeException(sprintf('Could not open file "%s".', $this->filename));
        }

        return Json::decode($content, true);
    }
}
