<?php

namespace Alchemy\Phraseanet;

/**
 * Simple class to map friendly names to Phraseanet record field names
 * @package Alchemy\Phraseanet
 */
class FieldMap
{
    /**
     * @var string[][]
     */
    private $rawMap = array();

    /**
     * @var string[][]
     */
    private $fieldMap = array();

    /**
     * @param array $mappings An array of field mappings indexed by alias and locale, ie
     *                        array('alias' => array('fr' => 'phraseanetFieldName'))
     */
    public function __construct(array $mappings = array())
    {
        $this->rawMap = $mappings;

        foreach ($mappings as $alias => $localizedFields) {
            foreach ($localizedFields as $locale => $localizedField) {
                if (! isset($this->fieldMap[$locale])) {
                    $this->fieldMap[$locale] = array();
                }

                $this->fieldMap[$locale][$alias] = $localizedField;
            }
        }
    }

    /**
     * @return array|\string[]
     */
    public function toArray()
    {
        return $this->rawMap;
    }

    /**
     * Retrieves a field name from an alias
     *
     * @param string $key Alias of the field to fetch
     * @param string $locale Short locale name (eg. en)
     * @return string
     */
    public function getFieldName($key, $locale)
    {
        if (!isset($this->fieldMap[$locale][$key])) {
            throw new \OutOfBoundsException();
        }

        return $this->fieldMap[$locale][$key];
    }

    /**
     * Retrieves an alias from a localized source field name
     *
     * @param string $key Name of the field
     * @param string $locale
     * @return string
     */
    public function getAliasFromFieldName($key, $locale)
    {
        if (! isset($this->fieldMap[$locale])) {
            throw new \OutOfBoundsException();
        }

        $flipped = array_flip($this->fieldMap[$locale]);

        if (! isset($flipped[$key])) {
            throw new \OutOfBoundsException();
        }

        return $flipped[$key];
    }
}
