<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class SubDefinitionsNodeBuilder implements ConfigurationInterface
{

    private $name;

    public function __construct($rootName = 'subdefinitions')
    {
        $this->name = $rootName;
    }

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
        $node = $builder->root($this->name);

        $node
            ->useAttributeAsKey('name')
            ->prototype('scalar')
            ->isRequired()
            ->cannotBeEmpty();

        return $node;
    }
}
