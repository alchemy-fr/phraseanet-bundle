<?php

namespace Alchemy\Phraseanet;

use PhraseanetSDK\Entity\FeedEntry;
use PhraseanetSDK\Entity\FeedEntryItem;

/**
 * Class FeedHelper
 * @package Alchemy\Phraseanet
 * @deprecated Use FeedEntryFilter instead
 */
class FeedHelper
{
    private $pdfMimeTypes = array(
        'application/pdf',
        'application/x-pdf'
    );

    public function entryContainsPdfDocuments(FeedEntry $feedEntry)
    {
        return ! empty($this->filterPdfItems($feedEntry));
    }

    public function filterPdfItems(FeedEntry $feedEntry)
    {
        return $feedEntry->getItems()->filter(function (FeedEntryItem $item) {
            $record = $item->getRecord();

            return in_array($record->getMimeType(), $this->pdfMimeTypes);
        });
    }
}
