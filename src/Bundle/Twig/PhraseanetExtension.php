<?php

namespace Alchemy\PhraseanetBundle\Twig;

use Alchemy\Phraseanet\DefinitionMap;
use Alchemy\Phraseanet\FeedHelper as FeedPdfHelper;
use Alchemy\Phraseanet\MetadataHelper;
use Alchemy\Phraseanet\ThumbHelper;
use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\Story;

class PhraseanetExtension extends \Twig_Extension
{
    /**
     * @var array
     */
    private $subdefsMap;

    /**
     * @var ThumbHelper
     */
    private $thumbFetcher;

    /**
     * @var MetadataHelper
     */
    private $metadataHelper;

    /**
     * @var FeedPdfHelper
     */
    private $feedPdfHelper;

    public function __construct(
        DefinitionMap $subdefsMap,
        ThumbHelper $thumbFetcher,
        MetadataHelper $metadataHelper,
        FeedPdfHelper $feedPdfHelper
    ) {
        $this->subdefsMap = $subdefsMap;
        $this->thumbFetcher = $thumbFetcher;
        $this->metadataHelper = $metadataHelper;
        $this->feedPdfHelper = $feedPdfHelper;
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
            new \Twig_SimpleFunction('record_caption', [ $this, 'getRecordCaption' ]),
            new \Twig_SimpleFunction('story_caption', [ $this, 'getStoryCaption' ]),
            new \Twig_SimpleFunction('fetch_thumbnail', [ $this, 'fetchThumbnail' ]),
            new \Twig_SimpleFunction('feed_entry_has_pdf_documents', [
                $this->feedPdfHelper,
                'entryContainsPdfDocuments'
            ])
        );
    }

    public function fetchThumbnail($record, $thumbType = 'medium')
    {
        return $this->thumbFetcher->fetch($record, $thumbType);
    }

    public function getRecordCaption(Record $record, $field, $locale = null)
    {
        return $this->metadataHelper->getRecordField($record, $field, $locale);
    }

    public function getStoryCaption(Story $story, $field, $locale = null)
    {
        return $this->metadataHelper->getStoryField($story, $field, $locale);
    }

    public function getRecordMultiCaption(Record $record, $field, $locale = null)
    {
        return $this->metadataHelper->getRecordMultiField($record, $field, $locale);
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
