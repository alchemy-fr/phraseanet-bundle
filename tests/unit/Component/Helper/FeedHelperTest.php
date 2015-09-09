<?php

namespace Alchemy\Phraseanet\Tests\Helper;

use Alchemy\Phraseanet\Helper\FeedHelper;
use Doctrine\Common\Collections\ArrayCollection;
use PhraseanetSDK\Entity\FeedEntry;
use PhraseanetSDK\Entity\FeedEntryItem;
use PhraseanetSDK\Entity\Record;

class FeedHelperTest extends \PHPUnit_Framework_TestCase
{
    public function getFeedEntryItems()
    {
        $firstData = new \stdClass();
        $firstData->item_id = 0;
        $firstData->record = new \stdClass();
        $firstData->record->mime_type = 'application/pdf';

        $secondData = new \stdClass();
        $secondData->item_id = 1;
        $secondData->record = new \stdClass();
        $secondData->record->mime_type = 'text/plain';

        return [ $firstData, $secondData ];
    }

    public function testHelperReturnsPdfItemsFromFeed()
    {
        $feedData = new \stdClass();
        $feedData->items = $this->getFeedEntryItems();

        $feedEntry = new FeedEntry($feedData);
        $helper = new FeedHelper();

        $this->assertTrue($helper->entryContainsPdfDocuments($feedEntry));
        $this->assertCount(1, $helper->filterPdfItems($feedEntry));
        $this->assertEquals($feedEntry->getItems()[0], $helper->filterPdfItems($feedEntry)[0]);
    }
}
