<?php
/**
 * This file is part of BraincraftedCocurBundle.
 *
 * (c) 2013 Florian Eckerstorfer <florian@eckerstorfer.co>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Braincrafted\Bundle\CocurBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * BraincraftedCocurExtension
 *
 * @package    BraincraftedCocurBundle
 * @subpackage DependencyInjection
 * @author     Florian Eckerstorfer <florian@eckerstorfer.co
 * @copyright  2013 Florian Eckerstorfer
 * @license    http://opensource.org/licenses/MIT The MIT License
 *
 * @codeCoverageIgnore
 */
class BraincraftedCocurExtension extends Extension
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
        foreach ([ 'command', 'generator', 'renderer', 'services', 'writer' ] as $key) {
            $loader->load($key.'.xml');
        }

        $container->setParameter('braincrafted_cocur.build_directory', $config['build_directory']);
        $container->setParameter('braincrafted_cocur.index_name', $config['index_name']);
        $container->setParameter('braincrafted_cocur.base_url', $config['base_url']);
        $container->setParameter('braincrafted_cocur.routes', $config['routes']);
        $container->setParameter('braincrafted_cocur.enable_assetic', $this->getEnableAssetic($config, $container));

        $this->buildGenerators($container, $config['generators']);
    }

    /**
     * @param ContainerBuilder $container
     * @param array            $generators
     *
     * @return void
     */
    protected function buildGenerators(ContainerBuilder $container, array $generators)
    {
        $collection = $container->getDefinition('braincrafted_cocur.generator.collection');
        foreach ($generators as $name => $config) {
            $id = sprintf('%s.%s', $config['generator'], $name);
            $container
                ->setDefinition($id, new DefinitionDecorator($config['generator']))
                ->replaceArgument(0, $config['options']);

            $collection->addMethodCall('add', [ new Reference($id), $config['route'] ]);
        }
    }

    protected function getEnableAssetic(array $config, ContainerBuilder $container)
    {
        if (null !== $config['enable_assetic']) {
            return $config['enable_assetic'];
        }

        $bundles = $container->getParameter('kernel.bundles');
        if (true === isset($bundles['AsseticBundle'])) {
            return true;
        }

        return false;
    }
}
