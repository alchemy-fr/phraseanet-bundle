<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class SubDefinitionsNodeBuilder implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $this->getNode($treeBuilder);

        return $treeBuilder;
    }

    public function getNode(TreeBuilder $builder = null)
    {
        $builder = $builder ?: new TreeBuilder();
        $node = $builder->root('subdefinitions');

        $node
            ->useAttributeAsKey('name')
            ->prototype('scalar')
            ->isRequired()
            ->cannotBeEmpty();

        return $node;
    }
}
