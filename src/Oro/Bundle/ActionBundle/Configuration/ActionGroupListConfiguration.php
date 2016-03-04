<?php

namespace Oro\Bundle\ActionBundle\Configuration;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class ActionGroupListConfiguration implements ConfigurationInterface
{
    /**
     * @param array $configs
     * @return array
     */
    public function processConfiguration(array $configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration($this, [$configs]);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('action_groups');
        $root->useAttributeAsKey('name')
            ->prototype('array')
            ->children()
                ->arrayNode('arguments')
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->end()
                            ->scalarNode('message')->end()
                            ->scalarNode('default')->end()
                            ->booleanNode('required')
                                ->defaultFalse()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('conditions')
                    ->prototype('variable')->end()
                ->end()
                ->arrayNode('actions')
                    ->prototype('variable')->end()
                ->end()
            ->end()
        ->end();

        return $builder;
    }
}
