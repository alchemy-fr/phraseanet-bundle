<?php

namespace Alchemy\Phraseanet\Helper;

/**
 * Class InstanceHelper acts as a helper registry for a given instance.
 * @package Alchemy\Phraseanet\Helper
 */
class InstanceHelper
{
    /**
     * @var FeedHelper
     */
    private $feedHelper;

    /**
     * @var MetadataHelper
     */
    private $metadataHelper;

    /**
     * @var ThumbHelper
     */
    private $thumbHelper;

    public function __construct(FeedHelper $feedHelper, MetadataHelper $metadataHelper, ThumbHelper $thumbHelper)
    {
        $this->feedHelper = $feedHelper;
        $this->metadataHelper = $metadataHelper;
        $this->thumbHelper = $thumbHelper;
    }

    public function getFeedHelper()
    {
        return $this->feedHelper;
    }

    public function getMetadataHelper()
    {
        return $this->metadataHelper;
    }

    public function getThumbHelper()
    {
        return $this->thumbHelper;
    }
}
