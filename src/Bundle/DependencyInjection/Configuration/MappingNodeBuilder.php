<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class MappingNodeBuilder implements ConfigurationInterface
{

    private $rootName;

    public function __construct($rootName)
    {
        $this->rootName = $rootName;
    }

    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();

        $this->getNode($builder);

        return $builder;
    }

    public function getNode(TreeBuilder $builder = null)
    {
        $builder = $builder ?: new TreeBuilder();
        $node = $builder->root($this->rootName);

        $node
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->prototype('scalar')
                    ->isRequired()
                    ->cannotBeEmpty();

        return $node;
    }
}
