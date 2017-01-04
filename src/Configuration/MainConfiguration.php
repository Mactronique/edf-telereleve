<?php

/**
 * This file is part of Mactronique EDF TeleReleve package.
 *
 * @author Jean-Baptiste Nahan <jbnahan@gmail.com>
 * @copyright 2016 - Jean-Baptiste Nahan
 * @license MIT
 */
namespace Mactronique\TeleReleve\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class MainConfiguration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('main');

        // ... add node definitions to the root of the tree
        $rootNode
            ->children()
                ->scalarNode('compteur')
                    ->defaultValue('CBEMM')
                ->end()
                ->scalarNode('device')
                    ->defaultValue('/dev/ttyAMA0')
                ->end()
                ->arrayNode('storage')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('driver')
                            ->defaultValue('Sqlite')
                        ->end()
                        ->arrayNode('parameters')
                            ->defaultValue(['path'=>'datas.sqlite'])
                            ->prototype('variable')->end()
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('enable_email')
                    ->defaultValue(false)
                ->end()
                ->scalarNode('template')
                    ->defaultValue('default.text.twig')
                ->end()
                ->scalarNode('log_file')
                    ->defaultValue('telereleve.log')
                ->end()
                ->arrayNode('smtp')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('server')
                            ->defaultValue('127.0.0.1')
                        ->end()
                        ->integerNode('port')
                            ->defaultValue(25)
                        ->end()
                        ->scalarNode('security')
                            ->defaultValue(null)
                        ->end()
                        ->scalarNode('username')
                            ->defaultValue(null)
                        ->end()
                        ->scalarNode('password')
                            ->defaultValue(null)
                        ->end()
                        ->scalarNode('mime')
                            ->defaultValue('text/plain')
                        ->end()
                        ->arrayNode('from')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('display_name')
                                    ->defaultValue('TeleReleve')
                                ->end()
                                ->scalarNode('email')
                                    ->defaultValue('me@localhost')
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('to')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('display_name')
                                    ->defaultValue('Me')
                                ->end()
                                ->scalarNode('email')
                                    ->defaultValue('me@localhost')
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
