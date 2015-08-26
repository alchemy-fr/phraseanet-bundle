<?php

namespace Alchemy\Phraseanet\View;

use Alchemy\Phraseanet\DefinitionMap;
use Alchemy\Phraseanet\FieldMap;
use PhraseanetSDK\Entity\Record;

class RecordView
{
    /**
     * @var Record
     */
    private $record;

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
        Record $record,
        FieldMap $fieldMap,
        DefinitionMap $definitionMap,
        DefinitionMap $thumbnailMap
    ) {
        $this->record = $record;
        $this->fieldMap = $fieldMap;
        $this->definitionMap = $definitionMap;
        $this->thumbnailMap = $thumbnailMap;
    }

    /**
     * @return Record
     */
    public function getRecord()
    {
        return $this->record;
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
