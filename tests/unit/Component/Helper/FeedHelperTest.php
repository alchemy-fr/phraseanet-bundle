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
        $firstRecord = new Record();
        $firstRecord->setMimeType('application/pdf');

        $secondRecord = new Record();
        $secondRecord->setMimeType('text/plain');

        $first = new FeedEntryItem();
        $second = new FeedEntryItem();

        $first->setRecord($firstRecord);
        $second->setRecord($secondRecord);

        return new ArrayCollection([
            $first,
            $second
        ]);
    }

    public function testHelperReturnsPdfItemsFromFeed()
    {
        $feedEntry = new FeedEntry();
        $feedEntry->setItems($this->getFeedEntryItems());

        $helper = new FeedHelper();

        $this->assertTrue($helper->entryContainsPdfDocuments($feedEntry));
        $this->assertCount(1, $helper->filterPdfItems($feedEntry));
        $this->assertEquals($feedEntry->getItems()[0], $helper->filterPdfItems($feedEntry)[0]);
    }
}
