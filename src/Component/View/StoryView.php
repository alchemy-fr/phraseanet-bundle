<?php

namespace Alchemy\Phraseanet\View;

use Alchemy\Phraseanet\DefinitionMap;
use Alchemy\Phraseanet\FieldMap;
use PhraseanetSDK\Entity\Story;

class StoryView
{
    /**
     * @var Story
     */
    private $story;

    /**
     * @var DefinitionMap
     */
    private $definitionMap;

    /**
     * @var FieldMap
     */
    private $fieldMap;

    /**
     * @var DefinitionMap
     */
    private $thumbnailMap;

    public function __construct(
        Story $record,
        FieldMap $fieldMap,
        DefinitionMap $definitionMap,
        DefinitionMap $thumbnailMap
    ) {
        $this->story = $record;
        $this->fieldMap = $fieldMap;
        $this->definitionMap = $definitionMap;
        $this->thumbnailMap = $thumbnailMap;
    }

    /**
     * @return Story
     */
    public function getRecord()
    {
        return $this->story;
    }

    /**
     * @return FieldMap
     */
    public function getFieldMap()
    {
        return $this->fieldMap;
    }

    /**
     * @return DefinitionMap
     */
    public function getDefinitionMap()
    {
        return $this->definitionMap;
    }

    /**
     * @return DefinitionMap
     */
    public function getThumbnailMap()
    {
        return $this->thumbnailMap;
    }
}
