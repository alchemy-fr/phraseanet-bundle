<?php

namespace Alchemy\Phraseanet\Query;

use PhraseanetSDK\Repository\Record;
use PhraseanetSDK\Repository\Story;

class RecordQuery
{

    private $type = 0;

    private $query;

    /**
     * @param array $query
     * @param $type
     */
    public function __construct(array $query, $type)
    {
        $this->query = $query;
        $this->type = intval($type);
    }

    /**
     * @return array
     */
    public function getRawQuery()
    {
        return $this->query;
    }

	/**
	 * @param array $query
	 * @return RecordQuery
	 */
	public function setRawQuery($query)
	{
		$this->query = $query;
		return $this;
	}

    /**
     * @return int
     */
    public function getQueryType()
    {
        return $this->type;
    }

    /**
     * @param $repository
	 * @param int $pAPINumber API number (e.g. 3)
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function execute($repository, $pAPINumber = 1)
    {
        if (! $repository instanceof Record && ! $repository instanceof Story) {
            throw new \InvalidArgumentException('Invalid repository type (story or record required).');
        }

        $result = $repository->search($this->query, $pAPINumber);

        if ($this->type == 1) {
            return $result->getResults()->getStories();
        }

        return $result->getResults()->getRecords();
    }
}
