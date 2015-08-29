<?php

namespace Alchemy\Phraseanet\Helper;

use PhraseanetSDK\Entity\FeedEntry;
use PhraseanetSDK\Entity\FeedEntryItem;

class FeedEntryFilter
{
    /**
     * @var FeedEntry
     */
    private $feedEntry;

    /**
     * @var callable
     */
    private $filterCallback;

    /**
     * @param FeedEntry $feedEntry
     * @param callable $filterCallback
     */
    public function __construct(FeedEntry $feedEntry, callable $filterCallback)
    {
        $this->feedEntry = $feedEntry;
        $this->filterCallback = $filterCallback;
    }

    /**
     * @return bool
     */
    public function hasItems()
    {
        return ! empty($this->getItems());
    }

    /**
     * @return FeedEntryItem[]
     */
    public function getItems()
    {
        return array_filter($this->feedEntry->getItems()->toArray(), $this->filterCallback);
    }
}
