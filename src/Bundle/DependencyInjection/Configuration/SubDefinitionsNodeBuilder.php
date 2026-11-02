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
    public function getConfigTreeBuilder(): \Symfony\Component\Config\Definition\Builder\TreeBuilder
    {
        $treeBuilder = new TreeBuilder($this->name);

        $this->getNode($treeBuilder);

        return $treeBuilder;
    }

    public function getNode(?TreeBuilder $builder = null)
    {
        $builder = $builder ?: new TreeBuilder($this->name);
        $node = $builder->getRootNode();

        $node
            ->useAttributeAsKey('name')
            ->prototype('array')
            ->prototype('scalar')
            ->isRequired()
            ->cannotBeEmpty();

        return $node;
    }
}
