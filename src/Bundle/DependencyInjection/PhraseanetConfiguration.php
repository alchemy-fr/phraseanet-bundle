<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection;

use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\CacheNodeBuilder;
use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\MappingNodeBuilder;
use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\RepositoriesNodeBuilder;
use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\SubDefinitionsNodeBuilder;
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
                        ->booleanNode('extended')->defaultTrue()->end()
                        ->booleanNode('disable_ssl_verification')->defaultFalse()->end()
                        ->scalarNode('uploader')->end()
                        ->append((new CacheNodeBuilder())->getNode())
                        ->append((new MappingNodeBuilder('mappings'))->getNode())
                        ->append((new RepositoriesNodeBuilder())->getNode())
                        ->append((new SubDefinitionsNodeBuilder('subdefinitions'))->getNode())
                        ->append((new SubDefinitionsNodeBuilder('thumbnails'))->getNode())
                    ->end()
                ->end()
            ->end();
    }
}
