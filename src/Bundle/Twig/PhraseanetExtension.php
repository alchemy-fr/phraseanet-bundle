<?php

namespace Alchemy\PhraseanetBundle\Twig;

use Alchemy\Phraseanet\Helper\InstanceHelperRegistry;
use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\Story;

class PhraseanetExtension extends \Twig_Extension
{
    /**
     * @var InstanceHelperRegistry
     */
    private $helpers;

    public function __construct(InstanceHelperRegistry $helpers)
    {
        $this->helpers = $helpers;
    }

    public function getFilters()
    {
        return [
            'subdefs' => new \Twig_Filter_Method($this, 'subdefs'),
            'preview_subdefs' => new \Twig_Filter_Method($this, 'previewSubdefs'),
            'file_extension' => new \Twig_Filter_Method($this, 'extension'),
        ];
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('record_caption', [$this, 'getRecordCaption']),
            new \Twig_SimpleFunction('story_caption', [$this, 'getStoryCaption']),
            new \Twig_SimpleFunction('fetch_thumbnail', [$this, 'fetchThumbnail']),
            new \Twig_SimpleFunction('feed_entry_has_pdf_documents', [$this, 'entryContainsPdfDocuments'])
        );
    }

    public function fetchThumbnail($record, $thumbType = 'medium', $instanceName = null)
    {
        $thumbFetcher = $this->helpers->getHelper($instanceName)->getThumbHelper();

        return $thumbFetcher->fetch($record, $thumbType);
    }

    public function getRecordCaption(Record $record, $field, $locale = null, $instanceName = null)
    {
        $metadataHelper = $this->helpers->getHelper($instanceName)->getMetadataHelper();

        return $metadataHelper->getRecordField($record, $field, $locale);
    }

    public function getStoryCaption(Story $story, $field, $locale = null, $instanceName = null)
    {
        $metadataHelper = $this->helpers->getHelper($instanceName)->getMetadataHelper();

        return $metadataHelper->getStoryField($story, $field, $locale);
    }

    public function getRecordMultiCaption(Record $record, $field, $locale = null, $instanceName = null)
    {
        $metadataHelper = $this->helpers->getHelper($instanceName)->getMetadataHelper();

        return $metadataHelper->getRecordMultiField($record, $field, $locale);
    }

    public function previewSubdefs(Record $record, array $names)
    {
        return $this->subdefs($record, $names, 'preview');
    }

    public function subdefs(Record $record, array $names, $prefix = null)
    {
        $defsByName = array();
        $subdefs = array();

        foreach ($record->getSubdefs() as $subdef) {
            $defsByName[$subdef->getName()] = $subdef;
        }

        foreach ($names as $name) {
            $fullName = $prefix ? $prefix . '_' . $name : $name;

            if (!isset($this->subdefsMap[$fullName])) {
                throw new \RuntimeException('Subdef "' . $fullName . '" is not configured.');
            }

            $defName = $this->subdefsMap[$fullName];

            if (isset($defsByName[$defName])) {
                $subdefs[$name] = $defsByName[$defName];
            }
        }

        return $subdefs;
    }

    public function extension($name)
    {
        return strtolower(pathinfo($name, PATHINFO_EXTENSION));
    }

    public function getName()
    {
        return 'phraseanet';
    }
}
