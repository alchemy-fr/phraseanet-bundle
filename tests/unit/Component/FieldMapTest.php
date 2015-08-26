<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\FieldMap;

class FieldMapTest extends \PHPUnit_Framework_TestCase
{

    public function testToArrayReturnsDefinedFieldMappings()
    {
        $map = new FieldMap([ 'bacon' => [ 'fr' => 'ham' ] ]);

        $this->assertEquals([ 'bacon' => [ 'fr' => 'ham' ] ], $map->toArray());
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

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetUndefinedFieldInLocaleThrowsException()
    {
        $map = new FieldMap([
            'bacon' => [ 'en' => 'ham' ],
            'egg' => [ 'en' => 'yolk' ]
        ]);

        $map->getFieldName('bacon', 'fr');
    }

    public function testGetAliasFromFieldNameReturnsCorrectAlias()
    {
        $map = new FieldMap([ 'bacon' => [ 'fr' => 'jambon' ] ]);

        $this->assertEquals('bacon', $map->getAliasFromFieldName('jambon', 'fr'));
    }

    public function testHasAliasReturnsTrueWhenAliasIsDefined()
    {
        $map = new FieldMap([ 'bacon' => [ 'fr' => 'jambon' ] ]);

        $this->assertTrue($map->hasAlias('bacon', 'fr'));
    }

    public function testHasAliasReturnsFalseWhenAliasIsNotDefined()
    {
        $map = new FieldMap([ 'bacon' => [ 'fr' => 'jambon' ] ]);

        $this->assertFalse($map->hasAlias('bacon', 'en'));
        $this->assertFalse($map->hasAlias('milk', 'fr'));
    }

    public function testIsFieldMappedReturnsTrueWhenFieldIsMapped()
    {
        $map = new FieldMap([ 'bacon' => [ 'fr' => 'jambon' ] ]);

        $this->assertTrue($map->isFieldMapped('jambon', 'fr'));
    }

    public function testIsFieldMappedReturnsFalseWhenFieldIsNotMapped()
    {
        $map = new FieldMap([ 'bacon' => [ 'fr' => 'jambon' ] ]);

        $this->assertFalse($map->isFieldMapped('fromage', 'fr'));
        $this->assertFalse($map->isFieldMapped('jambon', 'en'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAliasFromUndefinedFieldNameThrowsException()
    {
        $map = new FieldMap([ 'bacon' => [ 'fr' => 'jambon' ] ]);

        $map->getAliasFromFieldName('fromage', 'fr');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetAliasFromUndefinedLocaleThrowsException()
    {
        $map = new FieldMap([ 'bacon' => [ 'fr' => 'jambon' ] ]);

        $map->getAliasFromFieldName('ham', 'en');
    }
}
