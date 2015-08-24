<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\FieldMap;

class FieldMapTest extends \PHPUnit_Framework_TestCase
{

    public function testToArrayReturnsDefinedFieldMappings()
    {
        $map = new FieldMap([ 'bacon' => 'ham' ]);

        $this->assertEquals([ 'bacon' => 'ham' ], $map->toArray());
    }

    public function testGetFieldNameReturnsMappedFieldName()
    {
        $map = new FieldMap([
            'bacon' => [ 'en' => 'ham'],
            'egg' => [ 'en' => 'yolk' ]
        ]);

        $this->assertEquals('yolk', $map->getFieldName('egg', 'en'));
        $this->assertEquals('ham', $map->getFieldName('bacon', 'en'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetUndefinedFieldThrowsException()
    {
        $map = new FieldMap([
            'bacon' => [ 'en' => 'ham' ],
            'egg' => [ 'en' => 'yolk' ]
        ]);

        $map->getFieldName('milk', 'en');
    }
}
