<?php

namespace Alchemy\Phraseanet;

/**
 * Simple class to map friendly names to Phraseanet record field names
 * @package Alchemy\Phraseanet
 */
class FieldMap
{
    /**
     * @var string[]
     */
    private $fieldMap;

    public function __construct(array $mappings = array())
    {
        $this->fieldMap = $mappings;
    }

    /**
     * @return array|\string[]
     */
    public function toArray()
    {
        return $this->fieldMap;
    }

    /**
     * @param string $key
     * @return string
     */
    public function getFieldName($key)
    {
        if (! isset($this->fieldMap[$key])) {
            throw new \OutOfBoundsException();
        }

        return $this->fieldMap[$key];
    }

    public function getAliasFromFieldName($key)
    {
        $flippedArray = array_flip($this->fieldMap);
    }
}
