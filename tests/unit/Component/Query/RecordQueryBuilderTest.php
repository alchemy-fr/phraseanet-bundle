<?php

namespace Alchemy\Phraseanet\Tests\Query;

use Alchemy\Phraseanet\Query\RecordQueryBuilder;
use PhraseanetSDK\Entity\DataboxCollection;
use PhraseanetSDK\Repository\Record;

class RecordQueryBuilderTest extends \PHPUnit\Framework\TestCase
{
    use \Prophecy\PhpUnit\ProphecyTrait;


    public function testSettingQueryTermCreatesQueryWithQueryField()
    {
        $builder = new RecordQueryBuilder();

        $builder->setQuery('bacon IS good');

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('query' => 'bacon IS good')), $actual);
    }

    public function testAddingCollectionEntityCreatesQueryWithBaseField()
    {
        $collection = $this->prophesize(DataboxCollection::class);
        $collection->getBaseId()->willReturn(12);

        $builder = new RecordQueryBuilder();

        $builder->addCollection($collection->reveal());

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('bases' => [12])), $actual);
    }

    public function testAddingOneCollectionCreatesQueryWithBaseField()
    {
        $builder = new RecordQueryBuilder();

        $builder->addCollection(12);

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('bases' => [12])), $actual);
    }

    public function testAddingMultipleCollectionsCreatesQueryWithBaseField()
    {
        $builder = new RecordQueryBuilder();

        $builder->addCollections(array(10, 12));

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('bases' => [10, 12])), $actual);
    }

    public function testSettingCollectionsReplacesExistingCollectionParameters()
    {
        $builder = new RecordQueryBuilder();

        $builder->addCollection(42);
        $builder->setCollections(array(12, 23));

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('bases' => [12, 23])), $actual);
    }

    public function testIntersectCollectionsFiltersOutOtheCollections()
    {
        $builder = new RecordQueryBuilder();

        $builder->addCollections(array(12, 23));
        $builder->intersectCollections(array(42, 12));

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('bases' => [12])), $actual);
    }

    public function testSetOffsetCreatesQueryWithOffsetParameter()
    {
        $builder = new RecordQueryBuilder();

        $builder->setOffset(42);

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('offset_start' => 42)), $actual);
    }

    public function setPageCreatesQueryWithOffsetParameter()
    {
        $builder = new RecordQueryBuilder();

        $builder->setLimit(20);
        $builder->setPage(2);

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('offset_start' => 40)), $actual);
    }

    public function testSetLimitCreateQueryWithLimitParameter()
    {
        $builder = new RecordQueryBuilder();

        $builder->setLimit(21);

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('per_page' => 21)), $actual);
    }

    public static function getSortParameterMappings()
    {
        return array(
            [RecordQueryBuilder::SORT_RANDOM, 'random'],
            [RecordQueryBuilder::SORT_CREATED_ON, 'created_on'],
            [RecordQueryBuilder::SORT_RELEVANCE, 'relevance']
        );
    }

    /**
     * @dataProvider getSortParameterMappings
     */
    public function testSortByCreatesQueryWithSortParameter($order, $queryValue)
    {
        $builder = new RecordQueryBuilder();

        $builder->sortBy($order, true);
        $actual = $builder->getQuery()->getRawQuery();
        $this->assertEquals(array_replace_recursive($actual, array('sort' => $queryValue, 'ord' => 'desc')), $actual);

        $builder->sortBy($order, false);
        $actual = $builder->getQuery()->getRawQuery();
        $this->assertEquals(array_replace_recursive($actual, array('sort' => $queryValue, 'ord' => 'asc')), $actual);
    }

    public static function getRecordTypeParameterMappings()
    {
        return array(
            [RecordQueryBuilder::RECORD_TYPE_AUDIO, 'audio'],
            [RecordQueryBuilder::RECORD_TYPE_DOCUMENT, 'document'],
            [RecordQueryBuilder::RECORD_TYPE_FLASH, 'flash'],
            [RecordQueryBuilder::RECORD_TYPE_IMAGE, 'image'],
            [RecordQueryBuilder::RECORD_TYPE_VIDEO, 'video']
        );
    }

    /**
     * @dataProvider getRecordTypeParameterMappings
     */
    public function testSetRecordTypeCreatesQueryWithRecordTypeParameter($recordType, $queryValue)
    {
        $builder = new RecordQueryBuilder();

        $builder->setRecordType($recordType);

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('record_type' => $queryValue)), $actual);
    }

    public function testInvalidRecordTypeThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $builder = new RecordQueryBuilder();

        $builder->setRecordType('bacon');
    }

    public function testDefaultSearchTypeIsRecordType()
    {
        $builder = new RecordQueryBuilder();

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('search_type' => 0)), $actual);
    }

    public function testSetSearchTypeCreatesQueryWithSearchTypeParameter()
    {
        $builder = new RecordQueryBuilder();

        $builder->setSearchType(RecordQueryBuilder::SEARCH_RECORDS);

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('search_type' => 0)), $actual);

        $builder->setSearchType(RecordQueryBuilder::SEARCH_STORIES);

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('search_type' => 1)), $actual);
    }

    public function testInvalidSearchTypeThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $builder = new RecordQueryBuilder();

        $builder->setSearchType(42);
    }

    public function testSetDateCriterionCreatesQueryWithDateFields()
    {
        $builder = new RecordQueryBuilder();

        $builder->setDateCriterion(
            'baconDLC',
            \DateTime::createFromFormat('Y-m-d', '2015-08-01'),
            \DateTime::createFromFormat('Y-m-d', '2015-08-31')
        );

        $actual = $builder->getQuery()->getRawQuery();
        $this->assertEquals(array_replace_recursive($actual, array(
            'date_field' => 'baconDLC',
            'date_min' => '2015/08/01',
            'date_max' => '2015/08/31'
        )), $actual);
    }

    public function testSetDateCriterionWithNoMaxCreatesQueryWithoutDateMaxField()
    {
        $builder = new RecordQueryBuilder();

        $builder->setDateCriterion(
            'baconDLC',
            \DateTime::createFromFormat('Y-m-d', '2015-08-01')
        );

        $actual = $builder->getQuery()->getRawQuery();
        $this->assertEquals(array_replace_recursive($actual, array(
            'date_field' => 'baconDLC',
            'date_min' => '2015/08/01'
        )), $actual);

        $this->assertArrayNotHasKey('date_max', $builder->getQuery()->getRawQuery());
    }

    public function testSetDateCriterionWithNoMinCreatesQueryWithoutDateMinField()
    {
        $builder = new RecordQueryBuilder();

        $builder->setDateCriterion(
            'baconDLC',
            null,
            \DateTime::createFromFormat('Y-m-d', '2015-08-01')
        );

        $actual = $builder->getQuery()->getRawQuery();
        $this->assertEquals(array_replace_recursive($actual, array(
            'date_field' => 'baconDLC',
            'date_max' => '2015/08/01'
        )), $actual);

        $this->assertArrayNotHasKey('date_min', $builder->getQuery()->getRawQuery());
    }

    public static function getInvalidDateFieldNames()
    {
        return array(
            [null],
            [''],
            [12],
            [true],
            [new \stdClass()],
            [array()]
        );
    }

    /**
     * @dataProvider getInvalidDateFieldNames
     */
    public function testSetDateCriterionWithInvalidFieldNameThrowsException($fieldName)
    {
        $this->expectException('InvalidArgumentException');

        $builder = new RecordQueryBuilder();

        $builder->setDateCriterion($fieldName, \DateTime::createFromFormat('Y-m-d', '2015-08-01'));
    }

    public function testSetDateCriterionWithNoMinAndNoMaxThrowsException()
    {
        $this->expectException('InvalidArgumentException');

        $builder = new RecordQueryBuilder();

        $builder->setDateCriterion('baconDLC');
    }

    public function testAddStatusCreatesQueryWithStatusField()
    {
        $builder = new RecordQueryBuilder();

        $builder->addStatus(1, true);

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('status' => [1 => true])), $actual);
    }

    public function testAddStatusesCreatesQueryWithStatusField()
    {
        $builder = new RecordQueryBuilder();

        $builder->addStatuses(array(1 => true, 2 => false, 3 => true));

        $actual = $builder->getQuery()->getRawQuery();
        $this->assertEquals(array_replace_recursive($actual, array('status' => [1 => true, 2 => false, 3 => true])), $actual);
    }

    public function testSetStatusesReplacesExistingStatusParameters()
    {
        $builder = new RecordQueryBuilder();

        $builder->addStatus(1, true);
        $builder->setStatuses(array(2 => false, 3 => true));

        $query = $builder->getQuery()->getRawQuery();

        $actual = $query;
        $this->assertEquals(array_replace_recursive($actual, array('status' => [2 => false, 3 => true])), $actual);

        $this->assertEquals(array(2 => false, 3 => true), $query['status']);
    }

    public function testAddFieldCreatesQueryWithFieldRestriction()
    {
        $builder = new RecordQueryBuilder();

        $builder->addField('bacon');

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('fields' => [ 'bacon' ])), $actual);
    }

    public static function getInvalidFieldNames()
    {
        return array(
            [null],
            [''],
            [12],
            [true],
            [new \stdClass()],
            [array()]
        );
    }

    /**
     * @dataProvider getInvalidFieldNames
     */
    public function testAddFieldUsingInvalidNameThrowsException($name)
    {
        $this->expectException('InvalidArgumentException');

        $builder = new RecordQueryBuilder();

        $builder->addField($name);
    }

    public function testAddFieldsCreatesQueryWithFieldRestrictions()
    {
        $builder = new RecordQueryBuilder();

        $builder->addFields(array('bacon', 'eggs'));

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('fields' => [ 'bacon', 'eggs' ])), $actual);
    }

    /**
     * @dataProvider getInvalidFieldNames
     */
    public function testAddsFieldsWithInvalidNameThrowsException($name)
    {
        $this->expectException('InvalidArgumentException');

        $builder = new RecordQueryBuilder();

        $builder->addFields(array('bacon', 'eggs', $name));
    }


    public function testSetFieldsCreatesQueryWithReplacedFieldRestrictions()
    {
        $builder = new RecordQueryBuilder();

        $builder->addFields(array('bacon', 'eggs'));
        $builder->setFields(array('ham', 'yolk'));

        $actual = $builder->getQuery()->getRawQuery();

        $this->assertEquals(array_replace_recursive($actual, array('fields' => [ 'ham', 'yolk' ])), $actual);
    }

    /**
     * @dataProvider getInvalidFieldNames
     */
    public function testSetFieldsWithInvalidNameThrowsException($name)
    {
        $this->expectException('InvalidArgumentException');

        $builder = new RecordQueryBuilder();

        $builder->setFields(array('bacon', 'eggs', $name));
    }
}
