<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\EntityManagerFactory;
use Alchemy\Phraseanet\TokenProvider;
use org\bovigo\vfs\vfsStream;
use PhraseanetSDK\Application;
use PhraseanetSDK\EntityManager;
use PhraseanetSDK\Repository\Story;
use Prophecy\Argument;

class EntityManagerFactoryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetEntityManagerReturnsManagerFromCorrectApplication()
    {
        $token = uniqid('bacon');

        $entityManager = $this->prophesize(EntityManager::class);
        $application = $this->prophesize(Application::class);

        $application->getEntityManager(
            Argument::exact($token),
            Argument::any()
        )->willReturn($entityManager->reveal());

        $tokenProvider = $this->prophesize(TokenProvider::class);
        $tokenProvider->getToken()->willReturn($token);

        $factory = new EntityManagerFactory($application->reveal(), $tokenProvider->reveal());

        $this->assertEquals($entityManager->reveal(), $factory->getEntityManager());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetEntityManagerThrowsExceptionWhenTokenIsNull()
    {
        $tokenProvider = $this->prophesize(TokenProvider::class);
        $tokenProvider->getToken()->willReturn(null);

        $application = $this->prophesize(Application::class);

        $factory = new EntityManagerFactory($application->reveal(), $tokenProvider->reveal());

        $factory->getEntityManager();
    }

    public function testGetRepositoryReturnsCorrectRepository()
    {
        $token = uniqid('bacon');

        $entityManager = $this->prophesize(EntityManager::class);
        $application = $this->prophesize(Application::class);
        $tokenProvider = $this->prophesize(TokenProvider::class);
        $repository = $this->prophesize(Story::class);

        $application->getEntityManager(
            Argument::exact($token),
            Argument::any()
        )->willReturn($entityManager->reveal());

        $tokenProvider->getToken()->willReturn($token);
        $entityManager->getRepository(Argument::exact('story'))
            ->willReturn($repository->reveal())->shouldBeCalled();

        $factory = new EntityManagerFactory($application->reveal(), $tokenProvider->reveal());
        $repository = $factory->getRepository('story');


        $this->assertInstanceOf(Story::class, $repository);
        // Triggers proxy initialization
        $repository->findById(1, 1);
    }

    public function testGetRepositoryWithProxyCachePassesCorrectOptionsToApplication()
    {
        $vfs = vfsStream::setup('root');
        $token = uniqid('bacon');

        $entityManager = $this->prophesize(EntityManager::class);
        $application = $this->prophesize(Application::class);
        $tokenProvider = $this->prophesize(TokenProvider::class);
        $repository = $this->prophesize(Story::class);

        $application->getEntityManager(
            Argument::exact($token),
            Argument::exact([ 'proxy.path' => $vfs->url('/proxies') ])
        )->willReturn($entityManager->reveal());

        $tokenProvider->getToken()->willReturn($token);
        $entityManager->getRepository(Argument::exact('story'))
            ->willReturn($repository->reveal());

        $factory = new EntityManagerFactory($application->reveal(), $tokenProvider->reveal());

        $factory->setProxyCacheDirectory($vfs->url('/proxies'));

        $repository = $factory->getRepository('story');

        $this->assertInstanceOf(Story::class, $repository);
        // Triggers proxy initialization
        $repository->findById(1, 1);
    }

    public function testGetRepositoryWithAnnotationCachePassesCorrectOptionsToApplication()
    {
        $vfs = vfsStream::setup('root');
        $token = uniqid('bacon');

        $entityManager = $this->prophesize(EntityManager::class);
        $application = $this->prophesize(Application::class);
        $tokenProvider = $this->prophesize(TokenProvider::class);
        $repository = $this->prophesize(Story::class);

        $application->getEntityManager(
            Argument::exact($token),
            Argument::exact([ 'annotation.path' => $vfs->url('/proxies') ])
        )->willReturn($entityManager->reveal());

        $tokenProvider->getToken()->willReturn($token);
        $entityManager->getRepository(Argument::exact('story'))
            ->willReturn($repository->reveal());

        $factory = new EntityManagerFactory($application->reveal(), $tokenProvider->reveal());

        $factory->setAnnotationCacheDirectory($vfs->url('/proxies'));

        $repository = $factory->getRepository('story');

        $this->assertInstanceOf(Story::class, $repository);
        // Triggers proxy initialization
        $repository->findById(1, 1);
    }
}
