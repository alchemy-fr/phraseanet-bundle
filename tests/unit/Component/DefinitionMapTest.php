<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\DefinitionMap;

class DefinitionMapTest extends \PHPUnit_Framework_TestCase
{

    public function testGetMapReturnsDefinedMap()
    {
        $map = new DefinitionMap(array('bacon' => 'high-res'));

        $this->assertEquals(array('bacon' => 'high-res'), $map->getMap());
    }

    public function testSubDefinitionsCanBeRetrievedByFriendlyName()
    {
        $map = new DefinitionMap(array('bacon' => 'high-res'));

        $this->assertEquals('high-res', $map->getSubDefinition('bacon'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGettingAnUndefinedSubdefinitionThrowsAnException()
    {
        $map = new DefinitionMap();

        $map->getSubDefinition('bacon');
    }

    public function testSubdefinitionsCanBeAddedAndRetrieved()
    {
        $map = new DefinitionMap();

        $map->addMapping('bacon', 'high-res');

        $this->assertEquals('high-res', $map->getSubDefinition('bacon'));
    }
}
