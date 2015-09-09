<?php

namespace Alchemy\Phraseanet\Tests\Helper;

use Alchemy\Phraseanet\Helper\FeedEntryFilter;
use Doctrine\Common\Collections\ArrayCollection;
use MyProject\Proxies\__CG__\OtherProject\Proxies\__CG__\stdClass;
use PhraseanetSDK\Entity\FeedEntry;
use PhraseanetSDK\Entity\FeedEntryItem;

class FeedEntryFilterTest extends \PHPUnit_Framework_TestCase
{

    public function getFeedEntryData()
    {
        $data = [ new \stdClass(), new \stdClass() ];

        $data[0]->item_id = 1;
        $data[1]->item_id = 2;

        return $data;
    }

    public function getFeedEntryItems()
    {
        $data = $this->getFeedEntryData();

        $first = new FeedEntryItem($data[0]);
        $second = new FeedEntryItem($data[1]);

        return new ArrayCollection([
            $first,
            $second
        ]);
    }

    public function testGetItemsReturnsItemsMatchingFilter()
    {
        $data = new \stdClass();
        $data->id = 1;
        $data->items = $this->getFeedEntryData();

        $feedEntry = new FeedEntry($data);

        $filter = new FeedEntryFilter($feedEntry, function (FeedEntryItem $item) {
            return $item->getId() == 1;
        });

        $this->assertCount(1, $filter->getItems());
        $this->assertTrue($filter->hasItems());
        $this->assertEquals($feedEntry->getItems()[1], $filter->getItems()[1]);
    }


    public function testFilterWithAlwaysFalseCallbackIsEmpty()
    {
        $data = new \stdClass();
        $data->id = 1;
        $data->items = $this->getFeedEntryData();

        $feedEntry = new FeedEntry($data);

        $filter = new FeedEntryFilter($feedEntry, function () {
            return false;
        });

        $this->assertCount(0, $filter->getItems());
        $this->assertFalse($filter->hasItems());
    }
}
