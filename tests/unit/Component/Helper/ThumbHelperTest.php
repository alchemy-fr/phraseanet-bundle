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

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getThumbnail()->willReturn(new Subdef(new \stdClass));

        $this->assertSame($record->reveal()->getThumbnail(), $thumbHelper->fetch($record->reveal(), 'unmapped'));
    }

    public function testFetchMappedThumbnailDefinitionReturnsMatchSubdefinition()
    {
        $definitionMap = new DefinitionMap([ 'high' => 'my_subdef' ]);
        $thumbHelper = new ThumbHelper($definitionMap);

        $thumbnail = new Subdef(new \stdClass());
        $mySubdef = new Subdef(new \stdClass);

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getSubdefs()->willReturn(new ArrayCollection([
            'thumbnail' => $thumbnail,
            'my_subdef' => $mySubdef
        ]));

        $this->assertSame($mySubdef, $thumbHelper->fetch($record->reveal(), 'high'));
    }

    public function testFetchMissingSubdefinitionReturnsDefaultThumbnail()
    {
        $definitionMap = new DefinitionMap([ 'low' => 'missing_subdef' ]);
        $thumbHelper = new ThumbHelper($definitionMap);

        $thumbnail = new Subdef(new \stdClass());
        $mySubdef = new Subdef(new \stdClass());

        $record = $this->prophesize('PhraseanetSDK\Entity\Record');
        $record->getThumbnail()->willReturn($thumbnail);
        $record->getSubdefs()->willReturn(new ArrayCollection([
            'my_subdef' => $mySubdef
        ]));

        $this->assertSame($thumbnail, $thumbHelper->fetch($record->reveal(), 'low'));
    }
}
