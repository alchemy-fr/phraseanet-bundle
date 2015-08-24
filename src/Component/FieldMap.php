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
     * @param string $key Alias of the field to fetch
     * @param string $locale Short locale name (eg. en)
     * @return string
     */
    public function getFieldName($key, $locale)
    {
        if (!isset($this->fieldMap[$key][$locale])) {
            throw new \OutOfBoundsException();
        }

        return $this->fieldMap[$key][$locale];
    }

    public function getAliasFromFieldName($key)
    {
        $flippedArray = array_flip($this->fieldMap);
    }
}
