<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection;

use Alchemy\Phraseanet\ApplicationTokenProvider;
use Alchemy\Phraseanet\ChainedTokenProvider;
use Alchemy\Phraseanet\Helper\FeedHelper;
use Alchemy\Phraseanet\Helper\InstanceHelper;
use Alchemy\Phraseanet\Helper\InstanceHelperRegistry;
use Alchemy\Phraseanet\Helper\MetadataHelper;
use Alchemy\Phraseanet\Helper\ThumbHelper;
use Alchemy\Phraseanet\Mapping\DefinitionMap;
use Alchemy\Phraseanet\EntityManagerFactory;
use Alchemy\Phraseanet\EntityManagerRegistry;
use Alchemy\Phraseanet\Mapping\FieldMap;
use Alchemy\PhraseanetBundle\DependencyInjection\Builder\GuzzleAdapterBuilder;
use PhraseanetSDK\Application;
use PhraseanetSDK\EntityManager;
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
        $locator = new FileLocator(__DIR__ . '/../Resources/config');
        $loader = new YamlFileLoader($container, $locator);

        $loader->load('services.yml');
        $loader->load('profiler.yml');

        $registry = new Definition(EntityManagerRegistry::class);
        $registry->setLazy(true);

        $helperRegistry = new Definition(InstanceHelperRegistry::class);

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
                $container->setAlias('phraseanet.helpers', 'phraseanet.helpers.' . $name);
            }

            $this->buildEntityRepositories($name, $configuration, $container);
            $this->buildInstanceHelpers($name, $configuration, $container, $helperRegistry);
        }

        $container->setDefinition('phraseanet.em_registry', $registry);
        $container->setDefinition('phraseanet.helper_registry', $helperRegistry);
    }

    protected function buildEntityManagerFactory(ContainerBuilder $container, array $configuration)
    {
        $adapterBuilder = new GuzzleAdapterBuilder();

        $application = new Definition(Application::class, [
            $adapterBuilder->buildDefinition($container, $configuration['connection']['url'], $configuration['cache']),
            $configuration['connection']['client_id'],
            $configuration['connection']['secret'],
        ]);

        if (isset($configuration['extended']) && $configuration['extended']) {
            $application->addMethodCall('setExtendedMode', [true]);
        }

        if (isset($configuration['disable_ssl_verification'])) {
            $application->addMethodCall('setSslVerification', [!$configuration['disable_ssl_verification']]);
        }

        $tokenProvider = new Definition(ChainedTokenProvider::class);

        $applicationTokenProvider = new Definition(ApplicationTokenProvider::class, [
            $configuration['connection']['token']
        ]);

        $tokenProvider->addMethodCall('setDefaultProvider', [ $applicationTokenProvider ]);

        $container->setDefinition('phraseanet.token_provider', $tokenProvider);

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
     * @param Definition $registry
     */
    protected function buildInstanceHelpers(
        $instanceName,
        array $mergedConfig,
        ContainerBuilder $container,
        Definition $registry
    ) {
        $baseKey = 'phraseanet.helpers.' . $instanceName;

        $container->setDefinition($baseKey . '.feeds', new Definition(FeedHelper::class));
        $container->setDefinition($baseKey . '.meta', new Definition(MetadataHelper::class, [
            new Definition(FieldMap::class, [$mergedConfig['mappings']]),
            'fr',
            'fr'
        ]));

        $container->setDefinition($baseKey . '.definitions', new Definition(DefinitionMap::class, [
            $mergedConfig['subdefinitions']
        ]));

        $container->setDefinition($baseKey . '.thumbs', new Definition(ThumbHelper::class, [
            new Definition(DefinitionMap::class, [ $mergedConfig['thumbnails'] ])
        ]));

        $container->setDefinition($baseKey, new Definition(InstanceHelper::class, [
            new Reference($baseKey . '.definitions'),
            new Reference($baseKey . '.feeds'),
            new Reference($baseKey . '.meta'),
            new Reference($baseKey . '.thumbs')
        ]));

        $container
            ->getDefinition($baseKey)
            ->addMethodCall('setTokenProvider', [ new Reference('phraseanet.token_provider') ]);

        $registry->addMethodCall('addHelper', [
            $instanceName,
            new Reference($baseKey)
        ]);
    }
}
