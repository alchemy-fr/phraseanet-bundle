<?php

namespace Alchemy\Phraseanet;

class EntityManagerRegistry
{
    /**
     * @var EntityManagerFactory[]
     */
    private $factories = [];

    /**
     * @var string
     */
    private $defaultManager = null;

    public function addEntityManagerFactory($instanceAlias, EntityManagerFactory $factory)
    {
        $this->factories[$instanceAlias] = $factory;
    }

    public function setDefaultEntityManager($instanceAlias)
    {
        $this->defaultManager = $instanceAlias;
    }

    public function getEntityManager($instanceAlias = null)
    {
        if ($instanceAlias == null) {
            return $this->getDefaultEntityManager();
        }

        return $this->factories[$instanceAlias]->getEntityManager();
    }

    /**
     * The entity manager of the application token, whoever is logged in.
     */
    public function getApplicationEntityManager($instanceAlias = null)
    {
        $alias = $instanceAlias ?: $this->defaultManager;

        return ($alias ? $this->factories[$alias] : reset($this->factories))->getApplicationEntityManager();
    }

    public function getDefaultEntityManager()
    {
        if ($this->defaultManager == null) {
            return reset($this->factories)->getEntityManager();
        }

        return $this->getEntityManager($this->defaultManager);
    }

    public function getRepository($instanceAlias, $name)
    {
        return $this->getEntityManager($instanceAlias)->getRepository($name);
    }

    public function getDefaultRepository($name)
    {
        return $this->getDefaultEntityManager()->getRepository($name);
    }
}
