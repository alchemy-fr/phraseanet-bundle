<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Builder;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class GuzzleAdapterBuilder
{

    /**
     * @param ContainerBuilder $container
     * @param string $instanceUrl
     * @return Definition
     */
    public function buildDefinition(ContainerBuilder $container, $instanceUrl, array $cacheConfig = null)
    {
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, $instanceUrl), FILE_APPEND);
        $plugins = $this->getPluginReferences($container);

        if ($cacheConfig && $cacheConfig['type'] !== 'none') {
            $plugins[] = $this->buildCachePluginDefinition($cacheConfig);
        }

        $adapterParameters = array(
            $instanceUrl,
            $_COOKIE['parade-standard-ml-lng'] ?? 'en',
            $plugins,
        );

        $definition = new Definition(
            'Alchemy\Phraseanet\PhraseanetSDK\Http\GuzzleAdapter',
            $adapterParameters
        );

        $definition->setFactory('Alchemy\Phraseanet\PhraseanetSDK\Http\GuzzleAdapter::create');

        return $definition;
    }

    protected function getPluginReferences(ContainerBuilder $container)
    {
        $pluginIds = $container->findTaggedServiceIds('phraseanet.plugin');
        $references = array();

        foreach ($pluginIds as $pluginId => $tags) {
            $references[] = new Reference($pluginId);
        }

        return $references;
    }

    protected function buildCachePluginDefinition(array $cacheConfig)
    {
        return (new CacheDefinitionBuilder())->buildCacheDefinition($cacheConfig);
    }
}
