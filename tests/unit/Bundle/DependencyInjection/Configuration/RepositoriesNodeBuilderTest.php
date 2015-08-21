<?php

namespace Alchemy\PhraseanetBundle\Tests\DependencyInjection\Configuration;

use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\RepositoriesNodeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class RepositoriesNodeBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigurationParsesRepositoriesCorrectly()
    {
        $rawData = <<<EOY
repositories:
    api.story: story
    api.records: record

EOY;

        $loader = new Yaml();
        $data = $loader->parse($rawData);

        $configuration = new RepositoriesNodeBuilder();
        $processor = new Processor();

        $mergedConfiguration = $processor->processConfiguration($configuration, $data);

        $this->assertEquals([
            'api.story' => 'story',
            'api.records' => 'record'
        ], $mergedConfiguration);
    }
}
