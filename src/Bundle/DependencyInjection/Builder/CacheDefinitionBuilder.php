<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Builder;

use PhraseanetSDK\Cache\CacheMiddleware;
use Symfony\Component\Cache\Adapter\ArrayAdapter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Adapter\MemcachedAdapter;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\DependencyInjection\Definition;

class CacheDefinitionBuilder
{

    public function buildCacheDefinition(array $cacheConfiguration)
    {
        switch ($cacheConfiguration['type']) {
            case 'redis':
                $pool = $this->buildRedisCache($cacheConfiguration);
                break;
            case 'memcached':
                $pool = $this->buildMemcachedCache($cacheConfiguration);
                break;
            case 'array':
                $pool = new Definition(ArrayAdapter::class);
                break;
            case 'file':
                $pool = new Definition(FilesystemAdapter::class, ['phraseanet', 0, $cacheConfiguration['path']]);
                break;
            default:
                throw new \RuntimeException("Not implemented.");
        }

        return new Definition(CacheMiddleware::class, [$pool, $cacheConfiguration['ttl']]);
    }

    private function buildRedisCache(array $cacheConfiguration)
    {
        $redis = new Definition(\Redis::class);
        $redis->addMethodCall('connect', [$cacheConfiguration['host'], $cacheConfiguration['port']]);

        return new Definition(RedisAdapter::class, [$redis, 'phraseanet']);
    }

    private function buildMemcachedCache(array $cacheConfiguration)
    {
        $memcached = new Definition(\Memcached::class);
        $memcached->addMethodCall('addServer', [$cacheConfiguration['host'], $cacheConfiguration['port']]);

        return new Definition(MemcachedAdapter::class, [$memcached, 'phraseanet']);
    }
}
