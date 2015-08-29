<?php

namespace unit\Component\Helper;

use Alchemy\Phraseanet\Helper\MetadataHelper;
use Alchemy\Phraseanet\Mapping\FieldMap;
use Doctrine\Common\Collections\ArrayCollection;
use PhraseanetSDK\Entity\Metadata;
use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\RecordCaption;
use PhraseanetSDK\Entity\Story;

class MetadataHelperTest extends \PHPUnit_Framework_TestCase
{

    private function buildCaptions(array $captions)
    {
        $collection = new ArrayCollection();

        foreach ($captions as $name => $value) {
            $caption = new RecordCaption();
            $caption->setName($name);
            $caption->setValue($value);

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
                $caption = new Metadata();
                $caption->setName($name);
                $caption->setValue($value);

                $collection->add($caption);
            }
        }

        return $collection;
    }

    public function testGetStoryFieldReturnsCorrectValue()
    {
        $map = new FieldMap([ 'ham' => [ 'en' => 'bacon' ] ]);
        $helper = new MetadataHelper($map, 'en', 'en');

        $story = new Story();
        $story->setCaption($this->buildCaptions([ 'bacon' => 'yummy' ]));

        $this->assertEquals('yummy', $helper->getStoryField($story, 'ham', 'en'));
    }

    public function testGetUndefinedStoryFieldReturnsEmptyString()
    {
        $map = new FieldMap([ 'ham' => [ 'en' => 'bacon' ] ]);
        $helper = new MetadataHelper($map, 'en', 'en');

        $story = new Story();
        $story->setCaption(new ArrayCollection());

        $this->assertEquals('', $helper->getStoryField($story, 'ham', 'en'));
    }

    public function testGetUnmappedStoryFieldReturnsEmptyString()
    {
        $map = new FieldMap([ 'ham' => [ 'en' => 'bacon' ] ]);
        $helper = new MetadataHelper($map, 'en', 'en');

        $story = new Story();
        $story->setCaption(new ArrayCollection());

        $this->assertEquals('', $helper->getStoryField($story, 'eggs', 'en'));
    }

    public function testGetRecordFieldsReturnsMappedValues()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = new Record();
        $record->setMetadata($this->buildMetadata([ 'bacon' => 'pig', 'yolk' => 'chicken', 'steak' => 'cow' ]));

        $this->assertEquals([
            'ham' => 'pig',
            'eggs' => 'chicken'
        ], $helper->getRecordFields($record, null, 'en'));
    }


    public function testGetRecordFieldsSubsetReturnsMappedValues()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = new Record();
        $record->setMetadata($this->buildMetadata([ 'bacon' => 'pig', 'yolk' => 'chicken', 'steak' => 'cow' ]));

        $this->assertEquals([
            'ham' => 'pig'
        ], $helper->getRecordFields($record, [ 'ham' ], 'en'));
    }

    public function testGetRecordFieldsMapsMultiValuedFields()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = new Record();
        $record->setMetadata($this->buildMetadata([
            'bacon' => [ 'pig', 'pork' ],
            'yolk' => 'chicken',
            'steak' => 'cow' ]));

        $this->assertEquals([
            'ham' => [ 'pig', 'pork' ]
        ], $helper->getRecordFields($record, [ 'ham' ], 'en'));
    }

    public function testGetRecordFieldReturnsMappedValue()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = new Record();
        $record->setMetadata($this->buildMetadata([
            'bacon' => [ 'pig', 'pork' ],
            'yolk' => 'chicken',
            'steak' => 'cow' ]));

        $this->assertEquals('chicken', $helper->getRecordField($record, 'eggs', 'en'));
    }

    public function testGetUnmappedRecordFieldReturnsEmptyValue()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = new Record();
        $record->setMetadata($this->buildMetadata([
            'bacon' => [ 'pig', 'pork' ],
        ]));

        $this->assertEquals('', $helper->getRecordField($record, 'steak', 'en'));
        $this->assertEquals('', $helper->getRecordField($record, 'eggs', 'en'));
    }

    public function testGetRecordMultiFieldReturnsMappedValuesArray()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = new Record();
        $record->setMetadata($this->buildMetadata([
            'bacon' => [ 'pig', 'pork' ],
        ]));

        $this->assertEquals([ 'pig', 'pork' ], $helper->getRecordMultiField($record, 'ham', 'en'));
    }

    public function testGetUnmappedRecordMultiFieldReturnsEmptyArray()
    {
        $map = new FieldMap([
            'ham' => [ 'en' => 'bacon' ],
            'eggs' => [ 'en' => 'yolk' ]
        ]);

        $helper = new MetadataHelper($map, 'en', 'en');

        $record = new Record();
        $record->setMetadata($this->buildMetadata([
            'steak' => [ 'cow', 'ox' ],
        ]));

        $this->assertEquals([ ], $helper->getRecordMultiField($record, 'steak', 'en'));
    }
}
