<?php

namespace Alchemy\Phraseanet;

use PhraseanetSDK\Application;
use PhraseanetSDK\Repository\AbstractRepository;
use ProxyManager\Configuration;
use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use ProxyManager\Proxy\LazyLoadingInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EntityManagerFactory
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var TokenProvider
     */
    private $tokenProvider;

    /**
     * @var string Annotation cache directory
     */
    private $annotationCacheDirectory;

    /**
     * @var string Proxy cache directory
     */
    private $proxyCacheDirectory;

    /**
     * @param Application $phraseanetApplication
     * @param TokenProvider $tokenProvider
     */
    public function __construct(
        Application $phraseanetApplication,
        TokenProvider $tokenProvider
    ) {
        $this->application = $phraseanetApplication;
        $this->tokenProvider = $tokenProvider;
    }

    /**
     * @param string $path
     */
    public function setAnnotationCacheDirectory($path)
    {
        $this->annotationCacheDirectory = $path;
    }

    /**
     * @param string $path
     */
    public function setProxyCacheDirectory($path)
    {
        $this->proxyCacheDirectory = $path;
    }

    /**
     * @return \PhraseanetSDK\EntityManager
     */
    public function getEntityManager()
    {
        $token = $this->tokenProvider->getToken();
        $options = $this->getOptions();

        if ($token !== null) {
            return $this->application->getEntityManager($token, $options);
        }

        throw new \RuntimeException('A user token or an application token is required.');
    }

    /**
     * @param $name
     * @return AbstractRepository
     */
    public function getRepository($name)
    {
        $configuration = $this->getProxyFactoryConfiguration();

        $factory = new LazyLoadingValueHolderFactory($configuration);
        $initializer = function (
            & $wrappedObject,
            LazyLoadingInterface $proxy,
            $method,
            array $parameters,
            & $initializer
        ) use ($name) {
            $initializer = null;
            $wrappedObject = $this->getEntityManager()->getRepository($name);

            return true;
        };

        return $factory->createProxy('PhraseanetSDK\Repository\\' . ucfirst($name), $initializer);
    }

    /**
     * Builds the options array used to build SDK entity managers
     *
     * @return array
     */
    private function getOptions()
    {
        $options = array();

        if ($this->proxyCacheDirectory) {
            $this->ensureDirectoryExists($this->proxyCacheDirectory);

            $options['proxy.path'] = $this->proxyCacheDirectory;
        }

        if ($this->annotationCacheDirectory) {
            $this->ensureDirectoryExists($this->annotationCacheDirectory);

            $options['annotation.path'] = $this->annotationCacheDirectory;
        }

        return $options;
    }

    /**
     * Creates a directory matching requested path if it does not exist
     *
     * @param string $path
     */
    private function ensureDirectoryExists($path)
    {
        if (!file_exists($path)) {
            mkdir($path, 755, true);
        }
    }

    /**
     * Creates a ProxyManager configuration instance
     *
     * @return Configuration
     */
    private function getProxyFactoryConfiguration()
    {
        $configuration = new Configuration();

        if ($this->proxyCacheDirectory) {
            $this->ensureDirectoryExists($this->proxyCacheDirectory);
            $configuration->setProxiesTargetDir($this->proxyCacheDirectory);
        }

        return $configuration;
    }
}
