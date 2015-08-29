<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection;

use Alchemy\Phraseanet\ApplicationTokenProvider;
use Alchemy\Phraseanet\Helper\FeedHelper;
use Alchemy\Phraseanet\Helper\InstanceHelper;
use Alchemy\Phraseanet\Helper\MetadataHelper;
use Alchemy\Phraseanet\Helper\ThumbHelper;
use Alchemy\Phraseanet\Mapping\DefinitionMap;
use Alchemy\Phraseanet\EntityManagerFactory;
use Alchemy\Phraseanet\EntityManagerRegistry;
use Alchemy\Phraseanet\Mapping\FieldMap;
use Alchemy\PhraseanetBundle\DependencyInjection\Builder\GuzzleAdapterBuilder;
use PhraseanetSDK\Application;
use PhraseanetSDK\EntityManager;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class PhraseanetExtension extends ConfigurableExtension
{
    public function getConfiguration(array $config, ContainerBuilder $container)
    {
        return new PhraseanetConfiguration();
    }

    public function getAlias()
    {
        return 'phraseanet';
    }

    /**
     * Configures the passed container according to the merged configuration.
     *
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $registry = new Definition(EntityManagerRegistry::class);

        foreach ($mergedConfig['instances'] as $name => $configuration) {
            $factory = $this->buildEntityManagerFactory($container, $configuration);
            $registry->addMethodCall('addEntityManagerFactory', [
                $name,
                $factory
            ]);

            $container->setDefinition('phraseanet.factories.' . $name, $factory);

            $entityManager = new Definition(EntityManager::class, [$name]);
            $entityManager->setFactory([
                new Reference('phraseanet.factories.' . $name),
                'getEntityManager'
            ]);

            $container->setDefinition('phraseanet.em.' . $name, $entityManager);

            if ($name == $mergedConfig['default_instance']) {
                $registry->addMethodCall('setDefaultEntityManager', [
                    $mergedConfig['default_instance']
                ]);

                $container->setAlias('phraseanet.em', 'phraseanet.em.' . $name);
            }

            $this->buildEntityRepositories($name, $configuration, $container);
            $this->buildInstanceHelpers($name, $configuration, $container);
        }

        $container->setDefinition('phraseanet.em_registry', $registry);
    }

    protected function buildEntityManagerFactory(ContainerBuilder $container, array $configuration)
    {
        $adapterBuilder = new GuzzleAdapterBuilder();

        $application = new Definition(Application::class, [
            $adapterBuilder->buildDefinition($container, $configuration['connection']['url'], $configuration['cache']),
            $configuration['connection']['client_id'],
            $configuration['connection']['secret'],
        ]);

        $application->addMethodCall('setExtendedMode', [true]);

        $tokenProvider = new Definition(ApplicationTokenProvider::class, [
            $configuration['connection']['token']
        ]);

        $factory = new Definition(EntityManagerFactory::class, [
            $application,
            $tokenProvider
        ]);

        $factory->addMethodCall(
            'setAnnotationCacheDirectory',
            array($container->getParameter('kernel.cache_dir') . '/phraseanet/annotations')
        );

        $factory->addMethodCall(
            'setProxyCacheDirectory',
            array($container->getParameter('kernel.cache_dir') . '/phraseanet/proxies')
        );

        return $factory;
    }

    /**
     * @param string $instanceName
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function buildEntityRepositories($instanceName, array $mergedConfig, ContainerBuilder $container)
    {
        foreach ($mergedConfig['repositories'] as $name => $repositoryKey) {
            $definition = new Definition(
                'PhraseanetSDK\Repository\AbstractRepository',
                array($repositoryKey)
            );

            $definition->setFactory([
                new Reference('phraseanet.factories.' . $instanceName),
                'getRepository'
            ]);

            $container->setDefinition($name, $definition);
        }
    }

    /**
     * @param string $instanceName
     * @param array $mergedConfig
     * @param ContainerBuilder $container
     */
    protected function buildInstanceHelpers($instanceName, array $mergedConfig, ContainerBuilder $container)
    {
        $baseKey = 'phraseanet.helpers.' . $instanceName;

        $container->setDefinition($baseKey . '.feeds', new Definition(FeedHelper::class));
        $container->setDefinition($baseKey . '.meta', new Definition(MetadataHelper::class, [
            new Definition(FieldMap::class, [$mergedConfig['mappings']]),
            'fr',
            'fr'
        ]));

        $container->setDefinition($baseKey . '.thumbs', new Definition(ThumbHelper::class, [
            new Definition(DefinitionMap::class, $mergedConfig['thumbnails'])
        ]));

        $container->setDefinition($baseKey, new Definition(InstanceHelper::class, [
            new Reference($baseKey . '.feeds'),
            new Reference($baseKey . '.meta'),
            new Reference($baseKey . '.thumbs')
        ]));
    }
}
