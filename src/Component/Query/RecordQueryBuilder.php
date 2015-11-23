<?php

namespace Alchemy\Phraseanet\Query;

use Alchemy\Phraseanet\Predicate\PredicateBuilder;
use PhraseanetSDK\Entity\DataboxCollection;

class RecordQueryBuilder
{

    const RECORD_TYPE_AUDIO = 'audio';

    const RECORD_TYPE_VIDEO = 'video';

    const RECORD_TYPE_IMAGE = 'image';

    const RECORD_TYPE_DOCUMENT = 'document';

    const RECORD_TYPE_FLASH = 'flash';

    const SEARCH_RECORDS = 0;

    const SEARCH_STORIES = 1;

    const SORT_RELEVANCE = 'relevance';

    const SORT_CREATED_ON = 'created_on';

    const SORT_RANDOM = 'random';

    /**
     * @var string
     */
    private $query = '';

    /**
     * @var PredicateBuilder
     */
    private $conditionBuilder;

    /**
     * @var int[] An array of collection ID's to search
     */
    private $collections = array();

    /**
     * @var int Offset of the first record to return
     */
    private $offsetStart = 0;

    /**
     * @var int Number of records to return
     */
    private $recordsPerPage = 10;

    /**
     * @var string|null One of the RECORD_TYPE_* constant values to restrict the search to a specific document type
     */
    private $recordType = null;

    /**
     * @var int One of the SEARCH_TYPE_* constant values to select the type of record to fetch
     */
    private $searchType = self::SEARCH_RECORDS;

    /**
     * @var string|null Name of a date field to use as a date criterion
     */
    private $dateCriterionField = null;

    /**
     * @var \DateTimeInterface|null
     */
    private $dateCriterionMin = null;

    /**
     * @var \DateTimeInterface|null
     */
    private $dateCriterionMax = null;

    /**
     * @var int[] An array of statuses to restrict the search to documents matching the given statuses
     */
    private $statuses = array();

    /**
     * @var string[] An array of fields names to return in the search results
     */
    private $fields = array();

    /**
     * @var bool Whether to sort in descending order
     */
    private $sortDescending = false;

    /**
     * @var string Type of sort to use
     */
    private $sortType = null;

    /**
     * Sets the record search query string. Format follows the same specification as the Phraseanet search
     * engine.
     *
     * @param $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return PredicateBuilder
     */
    public function getConditionBuilder()
    {
        if ($this->conditionBuilder === null) {
            $this->conditionBuilder = new PredicateBuilder();
        }

        return $this->conditionBuilder;
    }

    /**
     * Add a collection to the search criteria.
     *
     * @param DataboxCollection|int $collection A collection or collection ID.
     * @return $this
     */
    public function addCollection($collection)
    {
        if ($collection instanceof DataboxCollection) {
            $collection = $collection->getBaseId();
        }

        $this->collections[] = $collection;

        return $this;
    }

    /**
     * Adds a list of collections to the search criteria.
     *
     * @param array $collections An array of DataboxCollection instances or collection ID's.
     * @return $this
     */
    public function addCollections(array $collections)
    {
        foreach ($collections as $collection) {
            $this->addCollection($collection);
        }

        return $this;
    }

    /**
     * Sets the list of collections in which to search for records.
     *
     * @param array $collections An array of DataboxCollection instances or collection ID's. An empty array will clear
     * the collection restriction.
     *
     * @return $this
     */
    public function setCollections(array $collections)
    {
        $this->collections = array();

        $this->addCollections($collections);

        return $this;
    }

    /**
     * Intersects the current list of collections with the given collections.
     *
     * @param array $collections
     */
    public function intersectCollections(array $collections)
    {
        $this->collections = array_values(array_intersect($this->collections, $collections));
    }

    /**
     * Sets the offset of the first record to return.
     *
     * @param int $offset Index of the first record to return. Defaults to 0 if an invalid value is provided.
     *
     * @return $this
     */
    public function setOffset($offset)
    {
        $this->offsetStart = max(0, intval($offset));

        return $this;
    }

    /**
     * Sets the sort type for the query
     *
     * @param string $sort One of the SORT_* constant values.
     * @param bool $descending True to sort in descending order, false otherwise.
     * @return $this
     * @throws \InvalidArgumentException when the sort type is not of the SORT_* constant values..
     */
    public function sortBy($sort, $descending = false)
    {
        $allowedSorts = array(
            self::SORT_CREATED_ON,
            self::SORT_RANDOM,
            self::SORT_RELEVANCE
        );

        if (! in_array($sort, $allowedSorts, true)) {
            //throw new \InvalidArgumentException('Invalid sort type: ' . $sort);
        }

        $this->sortType = (string) $sort;
        $this->sortDescending = (bool) $descending;

        return $this;
    }

    /**
     * Sets the maximum number of records to return.
     *
     * @param int $limit Maximum number of records to return. Defaults to 1 if an invalid value is provided.
     *
     * @return $this
     */
    public function setLimit($limit)
    {
        $this->recordsPerPage = max(1, intval($limit));

        return $this;
    }

    /**
     * Filters the media type of records to return.
     *
     * @param string $recordType One of the QueryBuilder::RECORD_TYPE_* constant values.
     * @return $this
     * @throws \InvalidArgumentException when the record media type is not valid.
     */
    public function setRecordType($recordType)
    {
        $allowedTypes = array(
            self::RECORD_TYPE_AUDIO,
            self::RECORD_TYPE_DOCUMENT,
            self::RECORD_TYPE_FLASH,
            self::RECORD_TYPE_IMAGE,
            self::RECORD_TYPE_VIDEO
        );

        if (! in_array($recordType, $allowedTypes, true)) {
            throw new \InvalidArgumentException(
                sprintf('Record type must be one of the RECORD_TYPE_* values, %s given.', $recordType)
            );
        }

        $this->recordType = $recordType;

        return $this;
    }

    /**
     * Sets the type of record to search for.
     *
     * @param int $searchType One of the QueryBuilder::SEARCH_* constant values.
     * @return $this
     * @throws \InvalidArgumentException when the the search type if not valid.
     */
    public function setSearchType($searchType)
    {
        $allowedTypes = array(
            self::SEARCH_RECORDS,
            self::SEARCH_STORIES
        );

        if (! in_array($searchType, $allowedTypes, true)) {
            throw new \InvalidArgumentException('Search type must be one of the SEARCH_* values');
        }

        $this->searchType = $searchType;

        return $this;
    }

    /**
     * Sets a date filter on the records to return. At least one of $minDate or $maxDate arguments
     * must be specified.
     *
     * @param string $fieldName The name of the field on which to filter by date.
     * @param \DateTimeInterface $minDate The lower date boundary.
     * @param \DateTimeInterface $maxDate The upper date boundary.
     * @return $this
     * @throws \InvalidArgumentException when the field name is an invalid or empty string
     * @throws \InvalidArgumentException when both min and max date values are null.
     */
    public function setDateCriterion($fieldName, \DateTimeInterface $minDate = null, \DateTimeInterface $maxDate = null)
    {
        $this->validateFieldName($fieldName, 'Field name is required and must be a non-empty string.');

        if ($minDate == null && $maxDate == null) {
            throw new \InvalidArgumentException('At least one of min or max date must be provided');
        }

        $this->dateCriterionField = $fieldName;
        $this->dateCriterionMin = $minDate;
        $this->dateCriterionMax = $maxDate;

        return $this;
    }

    /**
     * Adds a status filter to the search
     *
     * @param mixed $status
     * @param $value
     * @return $this
     */
    public function addStatus($status, $value)
    {
        $this->statuses[$status] = $value;

        return $this;
    }

    /**
     * Adds a list of status filters to the search.
     *
     * @param array $statuses
     * @return $this
     */
    public function addStatuses(array $statuses)
    {
        foreach ($statuses as $status => $value) {
            $this->addStatus($status, $value);
        }

        return $this;
    }

    /**
     * Sets the status filter to the given list. An empty list clears the filter.
     *
     * @param array $statuses
     * @return $this
     */
    public function setStatuses(array $statuses)
    {
        $this->statuses = array();

        $this->addStatuses($statuses);

        return $this;
    }

    /**
     * Adds a field to the list of requested fields.
     *
     * @param string $fieldName
     * @return $this
     * @throws \InvalidArgumentException when the field name is an invalid or empty string
     */
    public function addField($fieldName)
    {
        $this->validateFieldName($fieldName, 'Field name is required and must be a non-empty string.');
        $this->fields[] = $fieldName;

        return $this;
    }

    private function validateFieldName($fieldName, $errorMessage)
    {
        if (! is_string($fieldName) || trim($fieldName) === '') {
            throw new \InvalidArgumentException($errorMessage);
        }
    }

    /**
     * Adds a list of fields to the list of requested fields.
     *
     * @param array $fields
     * @return $this
     * @throws \InvalidArgumentException when one of the field names is an invalid or empty string
     */
    public function addFields(array $fields)
    {
        foreach ($fields as $field) {
            $this->addField($field);
        }

        return $this;
    }

    /**
     * Sets the list of requested fields. An empty clears the filter and all fields will be returned.
     *
     * @param array $fields
     * @return $this
     * @throws \InvalidArgumentException when one of the field names is an invalid or empty string
     */
    public function setFields(array $fields)
    {
        $this->fields = array();

        $this->addFields($fields);

        return $this;
    }

    /**
     * Returns the built query.
     *
     * @return RecordQuery
     */
    public function getQuery()
    {
        $query = array(
            'query' => $this->buildQueryTerm(),
            'bases' => array_unique($this->collections),
            'offset_start' => $this->offsetStart,
            'per_page' => $this->recordsPerPage,
            'search_type' => $this->searchType
        );

        $query = $this->appendRecordType($query);
        $query = $this->appendDates($query);
        $query = $this->appendStatuses($query);
        $query = $this->appendFields($query);
        $query = $this->appendSort($query);

        return new RecordQuery($query, $this->searchType);
    }

    private function buildQueryTerm()
    {
        if (! $this->conditionBuilder) {
            return $this->query;
        }

        $compiler = new QueryPredicateVisitor();

        $this->conditionBuilder->endAllGroups();
        $this->conditionBuilder->andWhere($this->query);

        return $compiler->compile($this->conditionBuilder->getPredicate());
    }

    /**
     * @param $query
     * @return mixed
     */
    private function appendRecordType($query)
    {
        if ($this->recordType !== null) {
            $query['record_type'] = $this->recordType;

            return $query;
        }

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    private function appendDates($query)
    {
        if ($this->dateCriterionField !== null) {
            $query['date_field'] = $this->dateCriterionField;

            if ($this->dateCriterionMin) {
                $query['date_min'] = $this->dateCriterionMin->format('Y/m/d');
            }

            if ($this->dateCriterionMax) {
                $query['date_max'] = $this->dateCriterionMax->format('Y/m/d');

                return $query;
            }

            return $query;
        }

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    private function appendStatuses($query)
    {
        if (!empty($this->statuses)) {
            $query['status'] = $this->statuses;

            return $query;
        }

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    private function appendFields($query)
    {
        if (!empty($this->fields)) {
            $query['fields'] = $this->fields;

            return $query;
        }

        return $query;
    }

    /**
     * @param $query
     * @return mixed
     */
    private function appendSort($query)
    {
        if ($this->sortType !== null) {
            $query['sort'] = $this->sortType;
            $query['ord'] = $this->sortDescending ? 'desc' : 'asc';

            return $query;
        }

        return $query;
    }
}
