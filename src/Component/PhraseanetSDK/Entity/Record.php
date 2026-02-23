<?php

/*
 * This file is part of Phraseanet SDK.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phraseanet\PhraseanetSDK\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class Record
{
    /**
     * @param \stdClass[] $values
     * @return Record[]
     */
    public static function fromList(array $values)
    {
        $records = array();

        foreach ($values as $value) {
            $records[] = self::fromValue($value);
        }

        return $records;
    }

    /**
     * @param \stdClass $value
     * @return Record
     */
    public static function fromValue(\stdClass $value)
    {
        return new self($value);
    }

    /**
     * @var \stdClass
     */
    protected $source;

    /**
     * @var \DateTimeInterface
     */
    protected $updatedOn;

    /**
     * @var \DateTimeInterface
     */
    protected $createdOn;

    /**
     * @var Subdef
     */
    protected $thumbnail;

    /**
     * @var Technical[]
     */
    protected $technicalInformation;

    /**
     * @var Metadata[]
     */
    protected $metadata;

    /**
     * @var Subdef[]
     */
    protected $subdefs;

    /**
     * @var RecordStatus[]
     */
    protected $status;

    /**
     * @var RecordCaption[]
     */
    protected $caption;

    /**
     * @param \stdClass $source
     */
    public function __construct(\stdClass $source)
    {
        $this->source = $source;
    }

    /**
     * @return \stdClass
     */
    public function getRawData()
    {
        return $this->source;
    }

    /**
     * Get unique id
     *
     * @return string
     */
    public function getId()
    {
        return $this->getDataboxId() . '_' . $this->getRecordId();
    }

    /**
     * Get the record id
     *
     * @return string
     */
    public function getRecordId()
    {
        return $this->source->record_id;
    }

    public function getAssetId(): string
    {
        return $this->source->record_id;
    }
    public function getTrackingId(): string
    {
        return $this->source->tracking_id ?? $this->source->record_id;
    }
    public function getTrackingTitle(): string
    {
        return $this->getTitle();
    }
    public function getMetrics()
    {
        return $this->source->metrics;
    }

    /**
     * Get the databox id
     *
     * @return integer
     */
    public function getDataboxId()
    {
        return $this->source->databox_id;
    }

    /**
     * Get the base id.
     *
     * @return integer
     */
    public function getBaseId()
    {
        return $this->source->base_id;
    }

    /**
     * Get the record title
     *
     * @return string
     */
    public function getTitle()
    {
        return isset($this->source->title->default) ? $this->source->title->default : $this->source->title;
    }

    /**
     * Get the record mime type
     *
     * @return string | null
     */
    public function getMimeType()
    {
        return $this->source->mime;
    }

    /**
     * Get the record original name
     *
     * @return string
     */
    public function getOriginalName()
    {
        return $this->source->original_name;
    }

    /**
     * Last updated date
     *
     * @return \DateTimeInterface
     */
    public function getUpdatedOn()
    {
        return $this->updatedOn ?: $this->updatedOn = new \DateTime($this->source->updated_on);
    }

    /**
     * Creation date
     *
     * @return \DateTimeInterface
     */
    public function getCreatedOn()
    {
        return $this->createdOn ?: $this->createdOn = new \DateTime($this->source->created_on);
    }

    /**
     * Get the record collection id
     *
     * @return integer
     */
    public function getCollectionId()
    {
        return $this->source->collection_id;
    }

    /**
     * Get the record SHA256 hash
     *
     * @return string
     */
    public function getSha256()
    {
        return $this->source->sha256;
    }

    /**
     * Return the thumbnail of the record as a Alchemy\Phraseanet\PhraseanetSDK\Entity\Subdef object
     * if the thumbnail exists null otherwise
     *
     * @return Subdef|null
     */
    public function getThumbnail()
    {
        if (isset($this->source->subdefs->thumbnail)) {
            $thumbnail = $this->source->subdefs->thumbnail;
            $thumbnail->name = 'thumbnail';
        } elseif (isset($this->source->thumbnail)) {
            $thumbnail = $this->source->thumbnail;
        } else {
            return null;
        }

        return $this->thumbnail ?: $this->thumbnail = Subdef::fromValue($thumbnail);
    }

    /**
     * Get the Record phraseaType IMAGE|VIDEO|DOCUMENT etc..
     *
     * @return string
     */
    public function getPhraseaType()
    {
        $r = $this->source->type ?? 'UNKNOWN';
        // file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...) -------> %s\n", __FILE__, __LINE__, __FUNCTION__, var_export($this->source, true)), FILE_APPEND);
        return $r;
    }

    /**
     * Get the record UUID
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->source->uuid;
    }

    /**
     * Get a collection of Phraseanet\Entity\Technical data objects
     *
     * @return ArrayCollection|Technical[]
     */
    public function getTechnicalInformation()
    {
        if (isset($this->source->technical_informations)) {
            $technicalInformations = $this->source->technical_informations;
        } elseif (isset($this->source->metadata_tags)) {
            $technicalInformations = $this->source->metadata_tags;
        } else {
            $this->technicalInformation = new ArrayCollection();
        }

        return $this->technicalInformation ?: new ArrayCollection(Technical::fromList(
            $technicalInformations
        ));
    }

    /**
     * Return a collection of Alchemy\Phraseanet\PhraseanetSDK\Entity\Subdef for the record
     *
     * @return ArrayCollection|Subdef[]
     */
    public function getSubdefs()
    {
        if(!isset($this->subdefs)) {
            if (!isset($this->source->subdefs)) {
                $this->subdefs = new ArrayCollection();
            }
            else {
                $subdefs = $this->source->subdefs;
                if (is_object($subdefs)) {
                    $subdefs = get_object_vars($this->source->subdefs);
                }
                $this->subdefs = new ArrayCollection(Subdef::fromList($subdefs));
            }
        }
        return $this->subdefs;
    }

    /**
     * @return RecordStatus[]|ArrayCollection
     */
    public function getStatus()
    {
        if (! isset($this->source->status)) {
            $this->status = new ArrayCollection();
        }

        return $this->status ?: new ArrayCollection(RecordStatus::fromList($this->source->status));
    }

    /**
     * @return RecordCaption[]|ArrayCollection
     */
    public function getCaption()
    {
        if (! isset($this->source->caption)) {
            $this->caption = new ArrayCollection();
        } else {
            $caption = $this->source->caption;
            if (is_object($this->source->caption)) {
                $caption = get_object_vars($this->source->caption);
            }
        }

        return $this->caption ?: new ArrayCollection(RecordCaption::fromList($caption));
    }

    /**
     * @return Metadata[]|ArrayCollection
     */
    public function getMetadata()
    {
        if (! isset($this->source->metadata)) {
            // fallback on caption source
            $this->metadata = $this->getCaption();
        }

        return $this->metadata ?: new ArrayCollection(Metadata::fromList($this->source->metadata));
    }
}
