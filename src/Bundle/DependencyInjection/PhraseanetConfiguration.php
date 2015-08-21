<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection;

use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\MappingNodeBuilder;
use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\RepositoriesNodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class PhraseanetConfiguration implements ConfigurationInterface
{

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $root = $builder->root('phraseanet');
        $nodeBuilder = $root->children();

        $this->addSdkNode($nodeBuilder);
        $this->addOtherNodes($nodeBuilder);

        return $builder;
    }

    public function addSdkNode(NodeBuilder $builder)
    {
        $builder
            ->scalarNode('default_instance')->end()
            ->arrayNode('instances')
                ->requiresAtLeastOneElement()
                ->useAttributeAsKey('name')
                ->prototype('array')
                    ->children()
                        ->arrayNode('connection')
                            ->children()
                                ->scalarNode('client_id')->end()
                                ->scalarNode('secret')->end()
                                ->scalarNode('url')->end()
                                ->scalarNode('token')->end()
                            ->end()
                        ->end()
                        ->append((new MappingNodeBuilder('mappings'))->getNode())
                        ->append((new RepositoriesNodeBuilder())->getNode())
                    ->end()
                ->end()
            ->end();
    }

    public function addOtherNodes(NodeBuilder $builder)
    {
        $builder
            ->variableNode('cache')->end()
            ->variableNode('recorder')->end()
            ->variableNode('repositories')->end()
            ->variableNode('subdefs')->end()
            ->variableNode('thumbnails')->end();
    }
}
