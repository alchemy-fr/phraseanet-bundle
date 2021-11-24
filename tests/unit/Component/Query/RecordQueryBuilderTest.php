<?php

namespace Alchemy\Phraseanet\Tests\Query;

use Alchemy\Phraseanet\Query\RecordQueryBuilder;
use PhraseanetSDK\Entity\DataboxCollection;
use PhraseanetSDK\Repository\Record;

class RecordQueryBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testSettingQueryTermCreatesQueryWithQueryField()
    {
        $builder = new RecordQueryBuilder();

        $builder->setQuery('bacon IS good');

        $this->assertArraySubset(array('query' => 'bacon IS good'), $builder->getQuery()->getRawQuery());
    }

    public function testAddingCollectionEntityCreatesQueryWithBaseField()
    {
        $collection = $this->prophesize(DataboxCollection::class);
        $collection->getBaseId()->willReturn(12);

        $builder = new RecordQueryBuilder();

        $builder->addCollection($collection->reveal());

        $this->assertArraySubset(array('bases' => [12]), $builder->getQuery()->getRawQuery());
    }

    public function testAddingOneCollectionCreatesQueryWithBaseField()
    {
        $builder = new RecordQueryBuilder();

        $builder->addCollection(12);

        $this->assertArraySubset(array('bases' => [12]), $builder->getQuery()->getRawQuery());
    }

    public function testAddingMultipleCollectionsCreatesQueryWithBaseField()
    {
        $builder = new RecordQueryBuilder();

        $builder->addCollections(array(10, 12));

        $this->assertArraySubset(array('bases' => [10, 12]), $builder->getQuery()->getRawQuery());
    }

    public function testSettingCollectionsReplacesExistingCollectionParameters()
    {
        $builder = new RecordQueryBuilder();

        $builder->addCollection(42);
        $builder->setCollections(array(12, 23));

        $this->assertArraySubset(array('bases' => [12, 23]), $builder->getQuery()->getRawQuery());
    }

    public function testIntersectCollectionsFiltersOutOtheCollections()
    {
        $builder = new RecordQueryBuilder();

        $builder->addCollections(array(12, 23));
        $builder->intersectCollections(array(42, 12));

        $this->assertArraySubset(array('bases' => [12]), $builder->getQuery()->getRawQuery());
    }

    public function testSetOffsetCreatesQueryWithOffsetParameter()
    {
        $builder = new RecordQueryBuilder();

        $builder->setOffset(42);

        $this->assertArraySubset(array('offset_start' => 42), $builder->getQuery()->getRawQuery());
    }

    public function setPageCreatesQueryWithOffsetParameter()
    {
        $builder = new RecordQueryBuilder();

        $builder->setLimit(20);
        $builder->setPage(2);

        $this->assertArraySubset(array('offset_start' => 40), $builder->getQuery()->getRawQuery());
    }

    public function testSetLimitCreateQueryWithLimitParameter()
    {
        $builder = new RecordQueryBuilder();

        $builder->setLimit(21);

        $this->assertArraySubset(array('per_page' => 21), $builder->getQuery()->getRawQuery());
    }

    public function getSortParameterMappings()
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
        $this->assertArraySubset(array('sort' => $queryValue, 'ord' => 'desc'), $builder->getQuery()->getRawQuery());

        $builder->sortBy($order, false);
        $this->assertArraySubset(array('sort' => $queryValue, 'ord' => 'asc'), $builder->getQuery()->getRawQuery());
    }

    public function getRecordTypeParameterMappings()
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

        $this->assertArraySubset(array('record_type' => $queryValue), $builder->getQuery()->getRawQuery());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidRecordTypeThrowsException()
    {
        $builder = new RecordQueryBuilder();

        $builder->setRecordType('bacon');
    }

    public function testDefaultSearchTypeIsRecordType()
    {
        $builder = new RecordQueryBuilder();

        $this->assertArraySubset(array('search_type' => 0), $builder->getQuery()->getRawQuery());
    }

    public function testSetSearchTypeCreatesQueryWithSearchTypeParameter()
    {
        $builder = new RecordQueryBuilder();

        $builder->setSearchType(RecordQueryBuilder::SEARCH_RECORDS);

        $this->assertArraySubset(array('search_type' => 0), $builder->getQuery()->getRawQuery());

        $builder->setSearchType(RecordQueryBuilder::SEARCH_STORIES);

        $this->assertArraySubset(array('search_type' => 1), $builder->getQuery()->getRawQuery());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidSearchTypeThrowsException()
    {
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

        $this->assertArraySubset(array(
            'date_field' => 'baconDLC',
            'date_min' => '2015/08/01',
            'date_max' => '2015/08/31'
        ), $builder->getQuery()->getRawQuery());
    }

    public function testSetDateCriterionWithNoMaxCreatesQueryWithoutDateMaxField()
    {
        $builder = new RecordQueryBuilder();

        $builder->setDateCriterion(
            'baconDLC',
            \DateTime::createFromFormat('Y-m-d', '2015-08-01')
        );

        $this->assertArraySubset(array(
            'date_field' => 'baconDLC',
            'date_min' => '2015/08/01'
        ), $builder->getQuery()->getRawQuery());

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

        $this->assertArraySubset(array(
            'date_field' => 'baconDLC',
            'date_max' => '2015/08/01'
        ), $builder->getQuery()->getRawQuery());

        $this->assertArrayNotHasKey('date_min', $builder->getQuery()->getRawQuery());
    }

    public function getInvalidDateFieldNames()
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
     * @expectedException \InvalidArgumentException
     */
    public function testSetDateCriterionWithInvalidFieldNameThrowsException($fieldName)
    {
        $builder = new RecordQueryBuilder();

        $builder->setDateCriterion($fieldName, \DateTime::createFromFormat('Y-m-d', '2015-08-01'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetDateCriterionWithNoMinAndNoMaxThrowsException()
    {
        $builder = new RecordQueryBuilder();

        $builder->setDateCriterion('baconDLC');
    }

    public function testAddStatusCreatesQueryWithStatusField()
    {
        $builder = new RecordQueryBuilder();

        $builder->addStatus(1, true);

        $this->assertArraySubset(array('status' => [1 => true]), $builder->getQuery()->getRawQuery());
    }

    public function testAddStatusesCreatesQueryWithStatusField()
    {
        $builder = new RecordQueryBuilder();

        $builder->addStatuses(array(1 => true, 2 => false, 3 => true));

        $this->assertArraySubset(
            array('status' => [1 => true, 2 => false, 3 => true]),
            $builder->getQuery()->getRawQuery()
        );
    }

    public function testSetStatusesReplacesExistingStatusParameters()
    {
        $builder = new RecordQueryBuilder();

        $builder->addStatus(1, true);
        $builder->setStatuses(array(2 => false, 3 => true));

        $query = $builder->getQuery()->getRawQuery();

        $this->assertArraySubset(
            array('status' => [2 => false, 3 => true]),
            $query
        );

        $this->assertEquals(array(2 => false, 3 => true), $query['status']);
    }

    public function testAddFieldCreatesQueryWithFieldRestriction()
    {
        $builder = new RecordQueryBuilder();

        $builder->addField('bacon');

        $this->assertArraySubset(array('fields' => [ 'bacon' ]), $builder->getQuery()->getRawQuery());
    }

    public function getInvalidFieldNames()
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
     * @expectedException \InvalidArgumentException
     */
    public function testAddFieldUsingInvalidNameThrowsException($name)
    {
        $builder = new RecordQueryBuilder();

        $builder->addField($name);
    }

    public function testAddFieldsCreatesQueryWithFieldRestrictions()
    {
        $builder = new RecordQueryBuilder();

        $builder->addFields(array('bacon', 'eggs'));

        $this->assertArraySubset(array('fields' => [ 'bacon', 'eggs' ]), $builder->getQuery()->getRawQuery());
    }

    /**
     * @dataProvider getInvalidFieldNames
     * @expectedException \InvalidArgumentException
     */
    public function testAddsFieldsWithInvalidNameThrowsException($name)
    {
        $builder = new RecordQueryBuilder();

        $builder->addFields(array('bacon', 'eggs', $name));
    }


    public function testSetFieldsCreatesQueryWithReplacedFieldRestrictions()
    {
        $builder = new RecordQueryBuilder();

        $builder->addFields(array('bacon', 'eggs'));
        $builder->setFields(array('ham', 'yolk'));

        $this->assertArraySubset(array('fields' => [ 'ham', 'yolk' ]), $builder->getQuery()->getRawQuery());
    }

    /**
     * @dataProvider getInvalidFieldNames
     * @expectedException \InvalidArgumentException
     */
    public function testSetFieldsWithInvalidNameThrowsException($name)
    {
        $builder = new RecordQueryBuilder();

        $builder->setFields(array('bacon', 'eggs', $name));
    }

    public function testTruncationIsDisabledByDefault()
    {
        $builder = new RecordQueryBuilder();

        $this->assertFalse($builder->isTruncationEnabled(), 'Truncation should be disabled by default');
    }

    public function testTruncationCanBeEnabled()
    {
        $builder = new RecordQueryBuilder();

        $builder->enableTruncation();

        $this->assertTrue($builder->isTruncationEnabled(), 'Truncation should be enabled after calling enable');
    }

    public function testTruncationCanBeDisabled()
    {
        $builder = new RecordQueryBuilder();

        $builder->enableTruncation();
        $builder->disableTruncation();

        $this->assertFalse($builder->isTruncationEnabled(), 'Truncation should be disabled after calling disable');
    }
}
