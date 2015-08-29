<?php

namespace unit\Component\Helper;

use Alchemy\Phraseanet\Helper\ThumbHelper;
use Alchemy\Phraseanet\Mapping\DefinitionMap;
use Doctrine\Common\Collections\ArrayCollection;
use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\Subdef;

class ThumbHelperTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFetchThumbnailsForInvalidTypeThrowsException()
    {
        $thumbHelper = new ThumbHelper(new DefinitionMap());

        $thumbHelper->fetch(new \stdClass(), 'invalid_type');
    }

    public function testFetchUnmappedThumbnailDefinitionReturnsDefaultThumbnail()
    {
        $thumbHelper = new ThumbHelper(new DefinitionMap());

        $record = new Record();
        $record->setThumbnail(new Subdef());

        $this->assertSame($record->getThumbnail(), $thumbHelper->fetch($record, 'unmapped'));
    }

    public function testFetchMappedThumbnailDefinitionReturnsMatchSubdefinition()
    {
        $definitionMap = new DefinitionMap([ 'high' => 'my_subdef' ]);
        $thumbHelper = new ThumbHelper($definitionMap);

        $thumbnail = new Subdef();
        $mySubdef = new Subdef();

        $record = new Record();
        $record->setSubdefs(new ArrayCollection([
            'thumbnail' => $thumbnail,
            'my_subdef' => $mySubdef
        ]));

        $this->assertSame($mySubdef, $thumbHelper->fetch($record, 'high'));
    }

    public function testFetchMissingSubdefinitionReturnsDefaultThumbnail()
    {
        $definitionMap = new DefinitionMap([ 'low' => 'missing_subdef' ]);
        $thumbHelper = new ThumbHelper($definitionMap);

        $thumbnail = new Subdef();
        $mySubdef = new Subdef();

        $record = new Record();
        $record->setThumbnail($thumbnail);
        $record->setSubdefs(new ArrayCollection([
            'my_subdef' => $mySubdef
        ]));

        $this->assertSame($thumbnail, $thumbHelper->fetch($record, 'low'));
    }
}
