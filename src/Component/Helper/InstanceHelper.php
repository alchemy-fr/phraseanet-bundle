<?php

namespace Alchemy\Phraseanet\Helper;

use Alchemy\Phraseanet\ChainedTokenProvider;
use Alchemy\Phraseanet\Mapping\DefinitionMap;
use Alchemy\Phraseanet\TokenProvider;

/**
 * Class InstanceHelper acts as a helper registry for a given instance.
 * @package Alchemy\Phraseanet\Helper
 */
class InstanceHelper
{

    /**
     * @var DefinitionMap
     */
    private $definitionMap;

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

    /**
     * @var TokenProvider
     */
    private $tokenProvider;

    /**
     * @param DefinitionMap $definitionMap
     * @param FeedHelper $feedHelper
     * @param MetadataHelper $metadataHelper
     * @param ThumbHelper $thumbHelper
     */
    public function __construct(
        DefinitionMap $definitionMap,
        FeedHelper $feedHelper,
        MetadataHelper $metadataHelper,
        ThumbHelper $thumbHelper
    ) {
        $this->definitionMap = $definitionMap;
        $this->feedHelper = $feedHelper;
        $this->metadataHelper = $metadataHelper;
        $this->thumbHelper = $thumbHelper;

        $this->tokenProvider = new ChainedTokenProvider();
    }

    public function setTokenProvider(TokenProvider $tokenProvider)
    {
        $this->tokenProvider = $tokenProvider;
    }

    public function getTokenProvider()
    {
        return $this->tokenProvider;
    }

    public function getDefinitionMap()
    {
        return $this->definitionMap;
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
