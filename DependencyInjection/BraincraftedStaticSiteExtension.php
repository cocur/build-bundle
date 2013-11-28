<?php
/**
 * This file is part of BraincraftedStaticSiteBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\StaticSiteBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * BraincraftedStaticSiteExtension
 *
 * @package    BraincraftedStaticSiteBundle
 * @subpackage DependencyInjection
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 *
 * @codeCoverageIgnore
 */
class BraincraftedStaticSiteExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader(
            $container,
            new FileLocator(__DIR__.'/../Resources/config/services')
        );
        foreach ([ 'command', 'renderer', 'writer' ] as $key) {
            $loader->load($key.'.xml');
        }

        if (!isset($config['build_directory'])) {
            throw new \InvalidArgumentException('The option "braincrafted_static_site.build_directory must be set.');
        }
        $container->setParameter('braincrafted_static_site.build_directory', $config['build_directory']);

        if (!isset($config['index_name'])) {
            throw new \InvalidArgumentException('The option "braincrafted_static_site.index_name must be set.');
        }
        $container->setParameter('braincrafted_static_site.index_name', $config['index_name']);

        $container->setParameter('braincrafted_static_site.base_url', $config['base_url']);
    }
}
