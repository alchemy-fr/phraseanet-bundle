<?php

namespace Alchemy\Phraseanet\Helper;

use Alchemy\Phraseanet\Mapping\FieldMap;
use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\Story;

class MetadataHelper
{

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var FieldMap
     */
    private $fieldsMap;

    /**
     * @var string
     */
    private $fallbackLocale;

    /**
     * @param FieldMap $fieldsMap
     * @param string $defaultLocale
     * @param string $fallbackLocale
     */
    public function __construct(FieldMap $fieldsMap, $defaultLocale, $fallbackLocale)
    {
        $this->fieldsMap = $fieldsMap;
        $this->defaultLocale = $defaultLocale;
        $this->fallbackLocale = $fallbackLocale;
    }

    public function getStoryField(Story $story, $field, $locale = null)
    {
        if (! $this->fieldsMap->hasAlias($field, $locale)) {
            return '';
        }

        $key = $this->fieldsMap->getFieldName($field, $locale);

        foreach ($story->getCaption() as $captionField) {
            if ($key === $captionField->getName()) {
                return $captionField->getValue();
            }
        }
    }

    public function getRecordFields(Record $record, array $fields = null, $locale = null)
    {
        $map = [];

        foreach ($record->getMetadata() as $metadata) {
            if (! $this->fieldsMap->isFieldMapped($metadata->getName(), $locale)) {
                continue;
            }

            $alias = $this->fieldsMap->getAliasFromFieldName($metadata->getName(), $locale);

            if ($fields !== null && ! in_array($alias, $fields)) {
                continue;
            }

            $map = $this->appendValueToMap($map, $alias, $metadata->getValue());
        }

        return $map;
    }

    /**
     * @param array $map
     * @param string $alias
     * @param string $value
     * @return array
     */
    private function appendValueToMap($map, $alias, $value)
    {
        if (isset($map[$alias])) {
            if (! is_array($map[$alias])) {
                $map[$alias] = [ $map[$alias] ];
            }

            $map[$alias][] = $value;
        } else {
            $map[$alias] = $value;
        }

        return $map;
    }

    public function getRecordField(Record $record, $field, $locale = null)
    {
        if (! $this->fieldsMap->hasAlias($field, $locale)) {
            return null;
        }

        $key = $this->fieldsMap->getFieldName($field, $locale);

        foreach ($record->getMetadata() as $metadata) {
            // Try to find the corresponding RecordCaption
            if ($key === $metadata->getName()) {
                return $metadata->getValue();
            }
        }

        return null;
    }

    public function getRecordMultiField(Record $record, $field, $locale = null)
    {
        if (! $this->fieldsMap->hasAlias($field, $locale)) {
            return [];
        }

        $key = $this->fieldsMap->getFieldName($field, $locale);
        $values = array();

        foreach ($record->getMetadata() as $metadata) {
            // Try to find the corresponding RecordCaption
            if ($key === $metadata->getName()) {
                $values[] = $metadata->getValue();
            }
        }

        return $values;
    }
}
