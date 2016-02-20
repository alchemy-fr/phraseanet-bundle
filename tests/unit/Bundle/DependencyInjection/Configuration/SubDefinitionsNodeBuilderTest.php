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
    low:
        default: small_preview
    medium:
        default: preview
    high:
        default: large_preview
EOY;

        $loader = new Yaml();
        $data = $loader->parse($rawData);

        $configuration = new SubDefinitionsNodeBuilder();
        $processor = new Processor();

        $mergedConfiguration = $processor->processConfiguration($configuration, $data);

        $this->assertEquals([
            'low' => [ 'default' => 'small_preview' ],
            'medium' => [ 'default' => 'preview' ],
            'high' => [ 'default' => 'large_preview' ]
        ], $mergedConfiguration);
    }
}
