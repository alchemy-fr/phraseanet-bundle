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

    public function addEntityManagerFactory($alias, EntityManagerFactory $factory)
    {
        $this->factories[$alias] = $factory;
    }

    public function setDefaultEntityManager($alias)
    {
        $this->defaultManager = $alias;
    }

    public function getEntityManager($alias)
    {
        return $this->factories[$alias]->getEntityManager();
    }

    public function getDefaultEntityManager()
    {
        if ($this->defaultManager == null) {
            return reset($this->factories)->getEntityManager();
        }

        return $this->getEntityManager($this->defaultManager);
    }
}
