<?php

namespace Alchemy\Phraseanet;

use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\Story;

class MetadataHelper
{

    private $defaultLocale;

    private $fieldsMap;

    private $fallbackLocale;

    public function __construct($fieldsMap, $defaultLocale, $fallbackLocale)
    {
        $this->fieldsMap = $fieldsMap;
        $this->defaultLocale = $defaultLocale;
        $this->fallbackLocale = $fallbackLocale;
    }

    public function getStoryField(Story $story, $field, $locale = null)
    {
        // fallback to caption
        $key = $this->getSourceKey($field, $locale);

        if (!$key) {
            return;
        }

        foreach ($story->getCaption() as $captionField) {
            // Try to find the corresponding RecordCaption
            if ($key === $captionField->getName()) {
                return $captionField->getValue();
            }
        }
    }

    public function getRecordFields(Record $record, array $fields = null, $locale = null)
    {
        if (!$fields) {
            $fields = array_keys($this->fieldsMap);
        }

        // Build a dictionary like [phraseanet_meta_key] => [local_field]
        $reverseFieldMap = array();
        foreach ($fields as $field) {
            $sourceKey = $this->getSourceKey($field, $locale);
            $reverseFieldMap[$sourceKey] = $field;
        }

        $map = array_fill_keys($fields, null);

        foreach ($record->getMetadata() as $metadata) {
            // Get local field from phraseanet caption name
            $sourceKey = $metadata->getName();
            if (isset($reverseFieldMap[$sourceKey])) {
                $field = $reverseFieldMap[$sourceKey];
            } else {
                continue;
            }

            // Store value in map
            $value = $metadata->getValue();
            if (isset($map[$sourceKey])) {
                // If we already have metadata on that key then it's a
                // multi-valued metadata.
                // We convert single value to an array of values
                if (is_array($map[$field])) {
                    $map[$field] = array($map[$sourceKey], $value);
                } else {
                    $map[$field][] = $value;
                }
            } else {
                // Single valued metadata or other values not found yet
                $map[$field] = $value;
            }
        }

        return $map;
    }

    public function getRecordField(Record $record, $field, $locale = null)
    {
        $key = $this->getSourceKey($field, $locale);

        if (!$key) {
            return null;
        }

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
        $key = $this->getSourceKey($field, $locale);

        if (!$key) {
            return;
        }

        $values = array();

        foreach ($record->getMetadata() as $metadata) {
            // Try to find the corresponding RecordCaption
            if ($key === $metadata->getName()) {
                $values[] = $metadata->getValue();
            }
        }

        return $values;
    }

    private function getSourceKey($field, $locale = null)
    {
        if (! isset($this->fieldsMap[$field])) {
            throw new \Exception(sprintf('The field "%s" is not mapped in remote instance configuration.', $field));
        }

        $fieldMap = $this->fieldsMap[$field];

        if (is_string($fieldMap)) {
            return $fieldMap;
        }

        $resolvedLocale = $locale ?: $this->defaultLocale;

        if (isset($fieldMap[$resolvedLocale])) {
            return $fieldMap[$resolvedLocale];
        }

        if (isset($fieldMap[$this->fallbackLocale])) {
            return $fieldMap[$this->fallbackLocale];
        }

        return '';
    }

    public function getLegacyRecordMetadata($source)
    {
        $legacyLocales = array('en', 'fr');
        $fields = array();

        foreach ($this->fieldsMap as $field => $locales) {
            if (is_string($locales)) {
                // Keep original field
                $fields[$field] = $locales;
                // Emulate all legacy locales on that field
                $locales = array_fill_keys($legacyLocales, $locales);
            }

            foreach ($locales as $locale => $sourceKey) {
                // Prevent BC Break by flattening field with locale
                $fields[sprintf('%s_%s', $field, $locale)] = $sourceKey;
            }
        }

        // Fill map with all fields, for unknown values, we use null.
        $map = array_fill_keys(array_keys($fields), null);

        foreach ($source as $metadata) {
            $key = array_search($metadata->getName(), $fields, true);
            if ($key) {
                $map[$key] = $metadata->getValue();
            }
        }

        return $map;
    }
}
