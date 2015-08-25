<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class PhraseanetExtension extends ConfigurableExtension
{
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new PhraseanetConfiguration();
    }

    /**
     * Configures the passed container according to the merged configuration.
     *
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $this->loadServicesFromConfig($container);

        $this->buildSdkApplicationService($mergedConfig, $container);
        $this->buildEntityManagerService($mergedConfig, $container);
        $this->buildEntityRepositories($mergedConfig, $container);
        $this->buildSdkHelpers($mergedConfig, $container);

        $container->setParameter('phraseanet.subdefs', $mergedConfig['subdefs']);
    }

    public function getAlias()
    {
        return 'phraseanet';
    }

    /**
     * @param ContainerBuilder $container
     */
    protected function loadServicesFromConfig(ContainerBuilder $container)
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(__DIR__ . '/../Resources/config')
        );

        if ($container->getParameter('kernel.debug')) {
            $loader->load('profiler.yml');
        }

        $loader->load('services.yml');
    }

    /**
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function buildSdkApplicationService(array $mergedConfig, ContainerBuilder $container)
    {
        $sdk = (new Definition('PhraseanetSDK\Application', array(
            $this->buildHttpAdapterDefinition($mergedConfig, $container),
            $mergedConfig['sdk']['client-id'],
            $mergedConfig['sdk']['secret'],
        )))
            ->addMethodCall('setExtendedMode', array($mergedConfig['sdk']['extended-responses']));
        $container->setDefinition('phraseanet.sdk', $sdk);
    }

    /**
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     * @return Definition
     */
    protected function buildHttpAdapterDefinition(array $mergedConfig, ContainerBuilder $container)
    {
        $adapterParameters = array(
            $mergedConfig['sdk']['url'],
            $this->getPluginReferences($container)
        );

        $definition = new Definition(
            'PhraseanetSDK\Http\GuzzleAdapter',
            $adapterParameters
        );

        $definition->setFactory('PhraseanetSDK\Http\GuzzleAdapter::create');

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

    /**
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function buildEntityManagerService(array $mergedConfig, ContainerBuilder $container)
    {
        $entityManagerFactory = new Definition('Alchemy\PhraseanetBundle\Phraseanet\EntityManagerFactory', array(
            new Reference('phraseanet.sdk'),
            new Reference('security.token_storage'),
            $mergedConfig['sdk']['token']
        ));

        $entityManagerFactory->addMethodCall(
            'setAnnotationCacheDirectory',
            array($container->getParameter('kernel.cache_dir') . '/phraseanet/cache')
        );

        $entityManagerFactory->addMethodCall(
            'setProxyCacheDirectory',
            array($container->getParameter('kernel.cache_dir') . '/phraseanet/proxies')
        );

        $entityManager = (new Definition('PhraseanetSDK\EntityManager', array(
            $mergedConfig['sdk']['token']
        )))
            ->setFactory(array(new Reference('phraseanet.em_factory'), 'getEntityManager'));

        $container->setDefinition('phraseanet.em_factory', $entityManagerFactory);
        $container->setDefinition('phraseanet.em', $entityManager);
    }

    /**
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function buildEntityRepositories(array $mergedConfig, ContainerBuilder $container)
    {
        foreach ($mergedConfig['repositories'] as $name => $repositoryKey) {
            $definition = new Definition(
                'PhraseanetSDK\Repository\AbstractRepository',
                array($repositoryKey)
            );

            $definition->setFactoryService('phraseanet.em_factory')
                ->setFactoryMethod('getRepository');

            $container->setDefinition($name, $definition);
        }
    }

    /**
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function buildSdkHelpers(array $mergedConfig, ContainerBuilder $container)
    {
        $container->setDefinition('phraseanet.feedhelper', new Definition(
            'Alchemy\Phraseanet\FeedHelper'
        ));

        $container->setDefinition('phraseanet.metadatahelper', new Definition(
            'Alchemy\Phraseanet\MetadataHelper',
            array($mergedConfig['mapping'], 'fr', 'fr',)
        ));

        $container->setDefinition('phraseanet.thumbhelper', new Definition(
            'Alchemy\Phraseanet\ThumbHelper',
            array($mergedConfig['thumbnails'])
        ));
    }
}
