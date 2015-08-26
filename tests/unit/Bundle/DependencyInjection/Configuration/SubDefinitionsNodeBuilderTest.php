<?php

namespace Alchemy\PhraseanetBundle\Tests\DependencyInjection\Configuration;

use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\SubDefinitionsNodeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class SubDefinitionsNodeBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigurationParsesMappingCorrectly()
    {
        $rawData = <<<EOY
subdefinitions:
    low: small_preview
    medium: preview
    high: large_preview
EOY;

        $loader = new Yaml();
        $data = $loader->parse($rawData);

        $configuration = new SubDefinitionsNodeBuilder();
        $processor = new Processor();

        $mergedConfiguration = $processor->processConfiguration($configuration, $data);

        $this->assertEquals(array(
            'low' => 'small_preview',
            'medium' => 'preview',
            'high' => 'large_preview'
        ), $mergedConfiguration);
    }
}
