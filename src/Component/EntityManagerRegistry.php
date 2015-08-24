<?php

namespace Alchemy\Phraseanet;

class EntityManagerRegistry
{
    /**
     * @var EntityManagerFactory[]
     */
    private $factories = [];

    public function addEntityManagerFactory($alias, EntityManagerFactory $factory)
    {
        $this->factories[$alias] = $factory;
    }

    public function getEntityManager($alias)
    {
        return $this->factories[$alias]->getEntityManager();
    }
}
