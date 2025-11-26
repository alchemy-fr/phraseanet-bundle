<?php

namespace Alchemy\Phraseanet\PhraseanetSDK\Search;

use Alchemy\Phraseanet\PhraseanetSDK\Exception\RuntimeException;
use Alchemy\Phraseanet\PhraseanetSDK\AbstractRepository;

class SearchRepository extends AbstractRepository
{

    /**
     * Search for records or stories, returning only references to the matching entities.
     *
     * @param  mixed[] $parameters Query parameters
     * @return SearchResults object
     * @throws RuntimeException
     */
    public function search(array $parameters = array())
    {
        $parameters = array_merge([
            'search_type' => SearchResult::TYPE_RECORD
        ], $parameters);

        $response = $this->query('POST', 'v2/search/', array(), $parameters);

        if ($response->isEmpty()) {
            throw new RuntimeException('Response content is empty');
        }

        return SearchResults::fromValue($this->em, $parameters['search_type'], $response->getResult());
    }
}
