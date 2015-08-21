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
            'bacon' => 'ham',
            'egg' => 'yolk'
        ]);

        $this->assertEquals('yolk', $map->getFieldName('egg'));
        $this->assertEquals('ham', $map->getFieldName('bacon'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetUndefinedFieldThrowsException()
    {
        $map = new FieldMap([
            'bacon' => 'ham',
            'egg' => 'yolk'
        ]);

        $map->getFieldName('milk');
    }
}
