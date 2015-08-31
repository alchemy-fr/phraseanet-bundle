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
}
