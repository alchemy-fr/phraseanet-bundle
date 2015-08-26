<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class CacheNodeBuilder implements ConfigurationInterface
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
        $node = $builder->root('cache');

        $node
            ->treatNullLike(array('type' => 'none'))
            ->validate()
                ->always(function ($v) {
                    return $this->validate($v);
                })
            ->end()
            ->children()
            ->scalarNode('path')->end()
            ->scalarNode('host')->end()
            ->scalarNode('port')->end()
            ->scalarNode('ttl')
                ->validate()
                ->ifTrue(function ($value) {
                    return ! is_int($value) || $value < 0;
                })
                ->thenInvalid('TTL must be a non-negative integer')
                ->end()
            ->end()
            ->enumNode('type')
                ->values([ null, 'none', 'array', 'file', 'redis', 'memcached' ])
                ->treatNullLike('none')
            ->end()
            ->enumNode('validation')
                ->values([ 'skip', 'deny' ])
            ->end();


        return $node;
    }

    private function validate($value)
    {
        if (! is_array($value)) {
            return $value;
        }

        if ($value['type'] == 'redis' || $value == 'memcached') {
            $this->validateServerCache($value['type'], $value);
        }

        if ($value['type'] == 'file' && ! isset($value['path'])) {
            throw new \InvalidArgumentException("'path' property is required for 'file' cache");
        }

        if ($value['type'] !== 'none' && ! isset($value['validation'])) {
            throw new \InvalidArgumentException("'validation' property is required when cache is enabled.");
        }

        return $value;
    }

    private function validateServerCache($type, $value)
    {
        if (! isset($value['host'])) {
            throw new \InvalidArgumentException("'host' is required for '$type' cache");
        }

        if (! isset($value['port'])) {
            throw new \InvalidArgumentException("'port' is required for '$type' cache");
        }
    }
}
