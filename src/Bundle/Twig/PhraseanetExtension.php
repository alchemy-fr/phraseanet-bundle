<?php

namespace Alchemy\PhraseanetBundle\Twig;

use Alchemy\Phraseanet\Helper\InstanceHelperRegistry;
use PhraseanetSDK\Entity\FeedEntry;
use PhraseanetSDK\Entity\Record;
use PhraseanetSDK\Entity\Story;
use Parade\Component\Media\Meta\FieldMapRegistry;

class PhraseanetExtension extends \Twig_Extension
{
    /**
     * @var InstanceHelperRegistry
     */
    private $helpers;

    /**
     * @var FieldMapRegistry
     */
    private $fieldMapRegistry;

    public function __construct(InstanceHelperRegistry $helpers, FieldMapRegistry $fieldMapRegistry)
    {
        $this->helpers = $helpers;
        $this->fieldMapRegistry = $fieldMapRegistry;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('record_hash', [ $this, 'getRecordHash' ]),
            new \Twig_SimpleFunction('record_caption', [$this, 'getRecordCaption']),
            new \Twig_SimpleFunction('story_caption', [$this, 'getStoryCaption']),
            new \Twig_SimpleFunction('fetch_thumbnail', [$this, 'fetchThumbnail']),
            new \Twig_SimpleFunction('fetch_thumbnail_url', [$this, 'fetchThumbnailUrl']),
            new \Twig_SimpleFunction('feed_entry_has_pdf_documents', [$this, 'entryContainsPdfDocuments'])
        );
    }

    public function fetchThumbnail($record, $thumbType = 'medium', $instanceName = null)
    {
        $thumbFetcher = $this->helpers->getHelper($instanceName)->getThumbHelper();

        return $thumbFetcher->fetch($record, $thumbType);
    }

    public function fetchThumbnailUrl($record, $thumbType = 'media', $instanceName = null)
    {
        $subdef = $this->fetchThumbnail($record, $thumbType, $instanceName);

        if (! $subdef) {
            return '';
        }

        return $subdef->getPermalink()->getUrl();
    }

    public function getRecordHash(Record $record, $instanceName = null)
    {
        return base64_encode(json_encode([
            'i' => $instanceName,
            'm' => $this->fieldMapRegistry->getDefaultMapName(),
            'd' => $record->getDataboxId(),
            'r' => $record->getRecordId()
        ]));
    }

    /**
     * @param Record $record
     * @param $field
     * @param null $locale
     * @param null $instanceName
     * @return null|string
     */
    public function getRecordCaption(Record $record, $field, $locale = null, $instanceName = null)
    {
        $metadataHelper = $this->helpers->getHelper($instanceName)->getMetadataHelper();

        return $metadataHelper->getRecordField($record, $field, $locale);
    }

    /**
     * @param Story $story
     * @param $field
     * @param null $locale
     * @param null $instanceName
     * @return string
     */
    public function getStoryCaption(Story $story, $field, $locale = null, $instanceName = null)
    {
        $metadataHelper = $this->helpers->getHelper($instanceName)->getMetadataHelper();

        return $metadataHelper->getStoryField($story, $field, $locale);
    }

    /**
     * @param Record $record
     * @param $field
     * @param null $locale
     * @param null $instanceName
     * @return array
     */
    public function getRecordMultiCaption(Record $record, $field, $locale = null, $instanceName = null)
    {
        $metadataHelper = $this->helpers->getHelper($instanceName)->getMetadataHelper();

        return $metadataHelper->getRecordMultiField($record, $field, $locale);
    }

    public function entryContainsPdfDocuments(FeedEntry $feedEntry, $name = null)
    {
        $feedHelper = $this->helpers->getHelper($name)->getFeedHelper();

        return $feedHelper->entryContainsPdfDocuments($feedEntry);
    }

    public function getName()
    {
        return 'phraseanet';
    }
}
