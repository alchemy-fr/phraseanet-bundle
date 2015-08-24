<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\EntityManagerFactory;
use Alchemy\Phraseanet\EntityManagerRegistry;
use Alchemy\Phraseanet\TokenProvider;
use PhraseanetSDK\Application;
use PhraseanetSDK\EntityManager;
use Prophecy\Argument;

class EntityManagerRegistryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetDefaultEntityManager()
    {
        $token = uniqid('bacon');

        $tokenProvider = $this->prophesize(TokenProvider::class);
        $tokenProvider->getToken()->willReturn($token);

        $entityManager = $this->prophesize(EntityManager::class)->reveal();

        $application = $this->prophesize(Application::class);
        $application->getEntityManager(Argument::exact($token), Argument::any())->willReturn($entityManager);

        $registry = new EntityManagerRegistry();
        $factory = new EntityManagerFactory($application->reveal(), $tokenProvider->reveal());

        $registry->addEntityManagerFactory('bacon', $factory);

        $this->assertEquals($entityManager, $registry->getEntityManager('bacon'));
    }
}
