<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\FeedEntryFilter;
use Doctrine\Common\Collections\ArrayCollection;
use PhraseanetSDK\Entity\FeedEntry;
use PhraseanetSDK\Entity\FeedEntryItem;

class FeedEntryFilterTest extends \PHPUnit_Framework_TestCase
{

    public function getFeedEntryItems()
    {
        $first = new FeedEntryItem();
        $second = new FeedEntryItem();

        $first->setId(1);
        $second->setId(2);

        return new ArrayCollection([
            $first,
            $second
        ]);
    }

    public function testGetItemsReturnsItemsMatchingFilter()
    {
        $feedEntry = new FeedEntry();
        $feedEntry->setItems($this->getFeedEntryItems());

        $filter = new FeedEntryFilter($feedEntry, function (FeedEntryItem $item) {
            return $item->getId() == 1;
        });

        $this->assertCount(1, $filter->getItems());
        $this->assertTrue($filter->hasItems());
        $this->assertEquals($feedEntry->getItems()[0], $filter->getItems()[0]);
    }


    public function testFilterWithAlwaysFalseCallbackIsEmpty()
    {
        $feedEntry = new FeedEntry();
        $feedEntry->setItems($this->getFeedEntryItems());

        $filter = new FeedEntryFilter($feedEntry, function (FeedEntryItem $item) {
            return false;
        });

        $this->assertCount(0, $filter->getItems());
        $this->assertFalse($filter->hasItems());
    }
}
