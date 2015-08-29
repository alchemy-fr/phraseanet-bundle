<?php

namespace Alchemy\Phraseanet\Tests\Mapping;

use Alchemy\Phraseanet\Mapping\DefinitionMap;

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

    public function testHasDefinitionReturnsTrueWhenDefinitionIsMapped()
    {
        $map = new DefinitionMap(array('bacon' => 'high-res'));

        $this->assertTrue($map->hasSubDefinition('bacon'));
    }

    public function testHasDefinitionReturnsFalseWhenDefinitionIsNotMapped()
    {
        $map = new DefinitionMap(array('bacon' => 'high-res'));

        $this->assertFalse($map->hasSubDefinition('milk'));
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
