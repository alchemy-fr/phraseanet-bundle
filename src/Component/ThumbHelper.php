<?php

namespace Alchemy\Phraseanet;

use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\Story;

class ThumbHelper
{
    const DEFAULT_THUMBNAIL_SUBDEF_NAME = 'thumbnail';

    private $thumbnailsMap;

    public function __construct(array $thumbnailsMap)
    {
        $this->thumbnailsMap = $thumbnailsMap;
    }

    /**
     * Fetch correct thumbnail according to type (medium or large)
     *
     * @param Record|Story $record Record for which to fetch the thumbnail
     * @param string $type Size of the thumbnail
     * @return null|\PhraseanetSDK\Entity\Subdef
     * @throws \InvalidArgumentException
     */
    public function fetch($record, $type)
    {
        if (!($record instanceof Record) && !($record instanceof Story)) {
            throw new \InvalidArgumentException();
        }

        if (!isset($this->thumbnailsMap[$type])) {
            return $record->getThumbnail();
        }

        $subdefName = $this->thumbnailsMap[$type];

        if ($subdefName === self::DEFAULT_THUMBNAIL_SUBDEF_NAME) {
            return $record->getThumbnail();
        }

        if ($record->getSubdefs()->containsKey($subdefName)) {
            return $record->getSubdefs()->get($subdefName);
        }

        return $record->getThumbnail();
    }
}
