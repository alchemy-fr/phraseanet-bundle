<?php

namespace Alchemy\PhraseanetBundle\Tests\DependencyInjection;

use Alchemy\PhraseanetBundle\DependencyInjection\PhraseanetConfiguration;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Yaml\Yaml;

class PhraseanetConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testConfigurationParsesCorrectly()
    {
        $rawData = <<<EOY
phraseanet:
    default_instance: default
    instances:
        default:
            connection:
                client-id: test-id
                secret: test-secret
                url: test-url
                token: test-token
            cache:
                type: redis
                host: 127.0.0.1
                port: 6379
                validation: skip
            mappings:
                bacon:
                    en: ham
                    fr: jambon
                    es: jamon
            subdefinitions:
                low: preview
                medium: preview_X2
                large: preview_X4
            repositories:
                api.default.stories: story
                api.default.records: record

EOY;

        $loader = new Yaml();
        $data = $loader->parse($rawData);

        $configuration = new PhraseanetConfiguration();
        $processor = new Processor();

        $mergedConfiguration = $processor->processConfiguration($configuration, $data);

        // @todo Test resulting array
    }
}
