<?php

namespace Alchemy\PhraseanetBundle\Tests\DependencyInjection\Configuration;

use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\CacheNodeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class CacheNodeBuilderTest extends \PHPUnit\Framework\TestCase
{
    private function getProcessedConfiguration($source)
    {
        $loader = new Yaml();
        $data = $loader->parse($source);

        $configuration = new CacheNodeBuilder();
        $processor = new Processor();

        return $processor->processConfiguration($configuration, $data);
    }

    public function testConfigurationParsesNoCacheMappingCorrectly()
    {
        $rawData = <<<EOY
cache:
    type: none
EOY;
        $mergedConfiguration = $this->getProcessedConfiguration($rawData);

        $this->assertEquals(array(
            'type' => 'none'
        ), $mergedConfiguration);
    }

    public function testConfigurationParsesNullCacheMappingCorrectly()
    {
        $rawData = <<<EOY
cache:
    type: ~
EOY;
        $mergedConfiguration = $this->getProcessedConfiguration($rawData);

        $this->assertEquals(array(
            'type' => 'none'
        ), $mergedConfiguration);

        $rawData = <<<EOY
cache: ~
EOY;
        $mergedConfiguration = $this->getProcessedConfiguration($rawData);

        $this->assertEquals(array(
            'type' => 'none'
        ), $mergedConfiguration);
    }

    public function testConfigurationParsesArrayCacheMappingCorrectly()
    {
        $rawData = <<<EOY
cache:
    type: array
    validation: skip
EOY;
        $mergedConfiguration = $this->getProcessedConfiguration($rawData);

        $this->assertEquals(array(
            'type' => 'array',
            'validation' => 'skip'
        ), $mergedConfiguration);
    }


    public function testConfigurationParsesRedisCacheMappingCorrectly()
    {
        $rawData = <<<EOY
cache:
    type: redis
    host: localhost
    port: 6379
    validation: skip
EOY;
        $mergedConfiguration = $this->getProcessedConfiguration($rawData);

        $this->assertEquals(array(
            'type' => 'redis',
            'host' => 'localhost',
            'port' => 6379,
            'validation' => 'skip'
        ), $mergedConfiguration);
    }

    public function testConfigurationDetectsMissingRedisCacheParameters()
    {
        $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');

        $rawData = <<<EOY
cache:
    type: redis
    validation: skip
EOY;

        $this->getProcessedConfiguration($rawData);
    }


    public function testConfigurationDetectsMissingRedisHostCacheParameters()
    {
        $this->expectException('Symfony\Component\Config\Definition\Exception\InvalidConfigurationException');

        $rawData = <<<EOY
cache:
    type: redis
    port: 6379
    validation: skip
EOY;

        $this->getProcessedConfiguration($rawData);
    }
}
