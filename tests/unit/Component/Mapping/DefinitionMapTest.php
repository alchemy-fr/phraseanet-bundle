<?php

namespace Alchemy\Phraseanet\Tests\Mapping;

use Alchemy\Phraseanet\Mapping\DefinitionMap;

class DefinitionMapTest extends \PHPUnit_Framework_TestCase
{

    public function testToArrayReturnsDefinedMap()
    {
        $map = new DefinitionMap(array('bacon' => 'high-res'));

        $this->assertEquals(array('bacon' => [ 'default' => 'high-res' ]), $map->toArray());
    }

    public function testSubDefinitionsCanBeRetrievedByFriendlyName()
    {
        $map = new DefinitionMap(array('bacon' => 'high-res'));

        $this->assertEquals('high-res', $map->getSubDefinition('bacon'));
        $this->assertEquals('high-res', $map->getSubDefinition('bacon', 'default'));
    }

    public function testSubDefinitionsCaneRetrievedByFriendlyNameAndType()
    {
        $map = new DefinitionMap(array('bacon' => [ 'pan' => 'fry' ]));

        $this->assertEquals('fry', $map->getSubDefinition('bacon', 'pan'));
    }

    public function testHasDefinitionReturnsTrueWhenDefinitionIsMapped()
    {
        $map = new DefinitionMap(array('bacon' => 'high-res'));

        $this->assertTrue($map->hasSubDefinition('bacon'));
        $this->assertTrue($map->hasSubDefinition('bacon', 'default'));
    }

    public function testHasDefinitionReturnsFalseWhenDefinitionIsNotMapped()
    {
        $map = new DefinitionMap(array('bacon' => 'high-res'));

        $this->assertFalse($map->hasSubDefinition('milk'));
        $this->assertFalse($map->hasSubDefinition('milk', 'default'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGettingAnUndefinedSubdefinitionThrowsAnException()
    {
        $map = new DefinitionMap();

        $map->getSubDefinition('bacon');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGettingAnUndefinedSubdefinitionAndTypeThrowsAnException()
    {
        $map = new DefinitionMap();

        $map->getSubDefinition('bacon', 'default');
    }

    public function testSubdefinitionsCanBeAddedAndRetrieved()
    {
        $map = new DefinitionMap();

        $map->addMapping('bacon', 'high-res');

        $this->assertEquals('high-res', $map->getSubDefinition('bacon'));
        $this->assertEquals('high-res', $map->getSubDefinition('bacon', 'default'));
    }
}
