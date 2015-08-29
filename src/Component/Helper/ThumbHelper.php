<?php

namespace Alchemy\Phraseanet\Helper;

use Alchemy\Phraseanet\Mapping\DefinitionMap;
use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\Story;

class ThumbHelper
{

    const DEFAULT_THUMBNAIL_SUBDEF_NAME = 'thumbnail';

    /**
     * @var DefinitionMap
     */
    private $thumbnailMap;

    /**
     * @param DefinitionMap $thumbnailMap
     */
    public function __construct(DefinitionMap $thumbnailMap)
    {
        $this->thumbnailMap = $thumbnailMap;
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

        if (! $this->thumbnailMap->hasSubDefinition($type)) {
            return $record->getThumbnail();
        }

        $subdefinition = $this->thumbnailMap->getSubDefinition($type);

        if ($record->getSubdefs()->containsKey($subdefinition)) {
            return $record->getSubdefs()->get($subdefinition);
        }

        return $record->getThumbnail();
    }
}
