<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class RepositoriesNodeBuilder implements ConfigurationInterface
{
    /**
     * Generates the configuration tree builder.
     *
     * @return \Symfony\Component\Config\Definition\Builder\TreeBuilder The tree builder
     */
    public function getConfigTreeBuilder(): \Symfony\Component\Config\Definition\Builder\TreeBuilder
    {
        $treeBuilder = new TreeBuilder('repositories');

        $this->getNode($treeBuilder);

        return $treeBuilder;
    }

    public function getNode(?TreeBuilder $builder = null)
    {
        $builder = $builder ?: new TreeBuilder('repositories');
        $node = $builder->getRootNode();

        $node
            ->useAttributeAsKey('name')
            ->prototype('scalar')
            ->isRequired()
            ->cannotBeEmpty()
            ->validate()
            ->ifTrue(function ($value) {
                if ($value == 'search' || $value == 'orders') {
                    return false;
                }

                return ! class_exists("PhraseanetSDK\\Repository\\" . ucfirst($value));
            })
            ->thenInvalid('Repository type does not exist.');

        return $node;
    }
}
