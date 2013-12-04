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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
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
        foreach ([ 'command', 'generator', 'renderer', 'writer' ] as $key) {
            $loader->load($key.'.xml');
        }

        $container->setParameter('braincrafted_static_site.build_directory', $config['build_directory']);
        $container->setParameter('braincrafted_static_site.index_name', $config['index_name']);
        $container->setParameter('braincrafted_static_site.base_url', $config['base_url']);
        if (true === isset($config['generators'])) {
            $this->buildGenerators($container, $config['generators']);
        }
    }

    protected function buildGenerators(ContainerBuilder $container, array $generators)
    {
        $collection = $container->getDefinition('braincrafted_static_site.generator.collection');
        foreach ($generators as $name => $config) {
            $id = sprintf('%s.%s', $config['generator'], $name);
            $container
                ->setDefinition($id, new DefinitionDecorator($config['generator']))
                ->replaceArgument(0, $config['options']);

            $collection->addMethodCall('add', [ new Reference($id), $config['route'] ]);
        }
    }
}
