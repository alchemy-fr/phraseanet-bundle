<?php

namespace unit\Component\Helper;

use Alchemy\Phraseanet\Helper\MetadataHelper;
use Alchemy\Phraseanet\Mapping\FieldMap;
use Doctrine\Common\Collections\ArrayCollection;
use PhraseanetSDK\Entity\Metadata;
use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\RecordCaption;
use PhraseanetSDK\Entity\Story;
use PhraseanetSDK\EntityManager;

class MetadataHelperTest extends \PHPUnit_Framework_TestCase
{

    private function buildCaptions(array $captions)
    {
        $collection = new ArrayCollection();

        foreach ($captions as $name => $value) {
            $data = new \stdClass();
            $data->name = $name;
            $data->value = $value;

            $caption = new RecordCaption($data);

            $collection->add($caption);
        }

        return $collection;
    }

    private function buildMetadata(array $captions)
    {
        $collection = new ArrayCollection();

        foreach ($captions as $name => $values) {
            if (! is_array($values)) {
                $values = [ $values ];
            }

            foreach ($values as $value) {
                $data = new \stdClass();
                $data->name = $name;
                $data->value = $value;

                $caption = new Metadata($data);

                $collection->add($caption);
            }
        }

        return $collection;
    }

    private function getEntityManager()
    {
        $entityManager = $this->prophesize('PhraseanetSDK\EntityManager');

        return $entityManager->reveal();
    }

    public function testGetStoryFieldReturnsCorrectValue()
    {
        $map = new FieldMap([ 'ham' => [ 'en' => 'bacon' ] ]);
        $helper = new MetadataHelper($map, 'en', 'en');

        $story = $this->prophesize('PhraseanetSDK\Entity\Story');
        $story->getCaption()->willReturn($this->buildCaptions([ 'bacon' => 'yummy' ]));

        $this->assertEquals('yummy', $helper->getStoryField($story->reveal(), 'ham', 'en'));
    }

    public function testGetUndefinedStoryFieldReturnsEmptyString()
    {
        $map = new FieldMap([ 'ham' => [ 'en' => 'bacon' ] ]);
        $helper = new MetadataHelper($map, 'en', 'en');

        $story = $this->prophesize('PhraseanetSDK\Entity\Story');
        $story->getCaption()->willReturn(new ArrayCollection());

        $this->assertEquals('', $helper->getStoryField($story->reveal(), 'ham', 'en'));
    }

    public function testGetUnmappedStoryFieldReturnsEmptyString()
    {
        $map = new FieldMap([ 'ham' => [ 'en' => 'bacon' ] ]);
        $helper = new MetadataHelper($map, 'en', 'en');

        $story = new Story($this->getEntityManager(), new \stdClass());

        $this->assertEquals('', $helper->getStoryField($story, 'eggs', 'en'));
    }

    public function testGetRecordFieldsReturnsMappedValues()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getMetadata()->willReturn($this->buildMetadata([
            'bacon' => 'pig',
            'yolk' => 'chicken',
            'steak' => 'cow'
        ]));

        $this->assertEquals([
            'ham' => 'pig',
            'eggs' => 'chicken'
        ], $helper->getRecordFields($record->reveal(), null, 'en'));
    }


    public function testGetRecordFieldsSubsetReturnsMappedValues()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getMetadata()->willReturn($this->buildMetadata([
            'bacon' => 'pig',
            'yolk' => 'chicken',
            'steak' => 'cow'
        ]));

        $this->assertEquals([
            'ham' => 'pig'
        ], $helper->getRecordFields($record->reveal(), [ 'ham' ], 'en'));
    }

    public function testGetRecordFieldsMapsMultiValuedFields()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getMetadata()->willReturn($this->buildMetadata([
            'bacon' => [ 'pig', 'pork' ],
            'yolk' => 'chicken',
            'steak' => 'cow'
        ]));

        $this->assertEquals([
            'ham' => [ 'pig', 'pork' ]
        ], $helper->getRecordFields($record->reveal(), [ 'ham' ], 'en'));
    }

    public function testGetRecordFieldReturnsMappedValue()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getMetadata()->willReturn($this->buildMetadata([
            'bacon' => [ 'pig', 'pork' ],
            'yolk' => 'chicken',
            'steak' => 'cow'
        ]));

        $this->assertEquals('chicken', $helper->getRecordField($record->reveal(), 'eggs', 'en'));
    }

    public function testGetUnmappedRecordFieldReturnsEmptyValue()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getMetadata()->willReturn($this->buildMetadata([
            'bacon' => [ 'pig', 'pork' ],
        ]));

        $this->assertEquals('', $helper->getRecordField($record->reveal(), 'steak', 'en'));
        $this->assertEquals('', $helper->getRecordField($record->reveal(), 'eggs', 'en'));
    }

    public function testGetRecordMultiFieldReturnsMappedValuesArray()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getMetadata()->willReturn($this->buildMetadata([
            'bacon' => [ 'pig', 'pork' ],
        ]));

        $this->assertEquals([ 'pig', 'pork' ], $helper->getRecordMultiField($record->reveal(), 'ham', 'en'));
    }

    public function testGetUnmappedRecordMultiFieldReturnsEmptyArray()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getMetadata()->willReturn($this->buildMetadata([
            'steak' => [ 'cow', 'ox' ],
        ]));

        $this->assertEquals([ ], $helper->getRecordMultiField($record->reveal(), 'steak', 'en'));
    }
}
