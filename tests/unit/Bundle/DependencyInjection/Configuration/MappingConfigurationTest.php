<?php

namespace Alchemy\PhraseanetBundle\Tests\DependencyInjection\Configuration;

use Alchemy\PhraseanetBundle\DependencyInjection\Configuration\MappingNodeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class MappingConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigurationParsesMappingCorrectly()
    {
        $rawData = <<<EOY
mappings:
    bacon:
        en: ham
        fr: jambon
        es: jamon

EOY;

        $loader = new Yaml();
        $data = $loader->parse($rawData);

        $configuration = new MappingNodeBuilder('mappings');
        $processor = new Processor();

        $mergedConfiguration = $processor->processConfiguration($configuration, $data);

        $this->assertEquals(array('bacon' => [
            'en' => 'ham',
            'fr' => 'jambon',
            'es' => 'jamon'
        ]), $mergedConfiguration);
    }
}
