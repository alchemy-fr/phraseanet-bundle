<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Builder;

use Doctrine\Common\Cache\RedisCache;
use Guzzle\Cache\DoctrineCacheAdapter;
use Guzzle\Plugin\Cache\CachePlugin;
use Guzzle\Plugin\Cache\DefaultCacheStorage;
use Guzzle\Plugin\Cache\RevalidationInterface;
use PhraseanetSDK\Cache\CanCacheStrategy;
use PhraseanetSDK\Cache\RevalidationFactory;
use Symfony\Component\DependencyInjection\Definition;

class CacheDefinitionBuilder
{

    public function buildCacheDefinition(array $cacheConfiguration)
    {
        switch ($cacheConfiguration['type']) {
            case 'redis':
                $definition = $this->buildRedisCache($cacheConfiguration);
                break;
            case 'memcached':
            case 'array':
            case 'file':
            default:
                throw new \RuntimeException("Not implemented.");
        }

        $validation = $this->buildRevalidationDefinition($cacheConfiguration);
        $canCacheStrategy = new Definition(CanCacheStrategy::class);

        $cacheAdapter = new Definition(DoctrineCacheAdapter::class, [
            $definition
        ]);

        $cacheStorage = new Definition(DefaultCacheStorage::class, [
            $cacheAdapter, '', $cacheConfiguration['ttl']
        ]);

        return new Definition(CachePlugin::class, [[
            'storage' => $cacheStorage,
            'revalidation' => $validation,
            'can_cache' => $canCacheStrategy
        ]]);
    }

    private function buildRevalidationDefinition(array $cacheConfiguration)
    {
        $factory = new Definition(RevalidationFactory::class);
        $revalidation = new Definition(RevalidationInterface::class, [
            $cacheConfiguration['validation']
        ]);

        $revalidation->setFactory([ $factory, 'create' ]);

        return $revalidation;
    }

    private function buildRedisCache(array $cacheConfiguration)
    {
        $redisDefinition = new Definition(\Redis::class);
        $redisDefinition->addMethodCall('connect', [
            $cacheConfiguration['host'],
            $cacheConfiguration['port']
        ]);

        $definition = new Definition(RedisCache::class);
        $definition->addMethodCall('setRedis', [ $redisDefinition ]);

        return $definition;
    }
}
