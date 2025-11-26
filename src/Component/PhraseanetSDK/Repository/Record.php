<?php

namespace Alchemy\Phraseanet\PhraseanetSDK\Repository;

use Alchemy\Phraseanet\PhraseanetSDK\AbstractRepository;
use Alchemy\Phraseanet\PhraseanetSDK\Entity\Query;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\RuntimeException;
use Doctrine\Common\Collections\ArrayCollection;

class Record extends AbstractRepository
{
    /**
     * Find the record by its id that belongs to the provided databox
     *
     * @param integer $databoxId    The record databox id
     * @param integer $recordId     The record id
     * @param boolean $disableCache Bypass cache when fetching a single record
     * @return \Alchemy\Phraseanet\PhraseanetSDK\Entity\Record
     * @throws RuntimeException
     */
    public function findById($databoxId, $recordId, $disableCache = false)
    {
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(%s, %s, %s)\n", __FILE__, __LINE__, __FUNCTION__, $databoxId, $recordId, $disableCache), FILE_APPEND);
//        $path = sprintf('v1/records/%s/%s/', $databoxId, $recordId);
//        $query = [];
//
//        if (true === $disableCache) {
//            $query['t'] = time();
//        }
//
//        $response = $this->query('GET', $path, $query);
//
//        if (true !== $response->hasProperty('record')) {
//            throw new RuntimeException('Missing "record" property in response content');
//        }
//
//        return \Alchemy\Phraseanet\PhraseanetSDK\Entity\Record::fromValue($response->getProperty('record'));

        $page = 1;
        $response = $this->query(
            'GET',
            '/assets/' . urlencode($recordId),
            [],
            [],
            [
                'Accept' => 'application/ld+json'
            ]
        );

        if ($response->isEmpty()) {
            throw new RuntimeException('Response content is empty');
        }

        $phraseaResult = $response->getResult();

        $response = [
            'databox_id'             => $phraseaResult['workspace']['id'],
            'record_id'              => $phraseaResult['id'],
            'resource_id'            => $phraseaResult['id'],
            'mime_type'              => $phraseaResult['source']['type'],
            'title'                  => $phraseaResult['title'],
            'original_name'          => '?',
            'updated_on'             => $phraseaResult['updatedAt'],
            'created_on'             => $phraseaResult['createdAt'],
            'collection_id'          => $phraseaResult['referenceCollection']['id'],
            'base_id'                => $phraseaResult['referenceCollection']['id'],
            'sha256'                 => '?',
            'thumbnail'              => [
                'name'        => 'thumbnail',
                'permalink'   => [
                    'created_on'   => '?',
                    'id'           => '?',
                    'is_activated' => true,
                    'label'        => $phraseaResult['title'],
                    'updated_on'   => '?',
                    'page_url'     => '?',
                    'download_url' => '?',
                    'url'          => $phraseaResult['thumbnail']['file']['url'],
                ],
                'height'      => '?',
                'width'       => '?',
                'filesize'    => $phraseaResult['thumbnail']['file']['size'],
                'devices'     => [
                    'screen'
                ],
                'player_type' => 'IMAGE',
                'mime_type'   => $phraseaResult['thumbnail']['file']['type'],
                'substituted' => false,
                'created_on'  => '?',
                'updated_on'  => '?',
                'url'         => $phraseaResult['thumbnail']['file']['url'],
                'url_ttl'     => '?',
            ],
            'technical_informations' => [],
            'phrasea_type'           => '?',
            'uuid'                   => '?',
            'subdefs'                => [
                [
                    'name'        => 'document',
                    'permalink'   => [
                        'created_on'   => $phraseaResult['source']['createdAt'],
                        'id'           => $phraseaResult['source']['id'],
                        'is_activated' => true,
                        'label'        => $phraseaResult['title'],
                        'updated_on'   => $phraseaResult['source']['updatedAt'],
                        'page_url'     => $phraseaResult['source']['id'],
                        'download_url' => '?',
                        'url'          => $phraseaResult['source']['url'],
                    ],
                    'height'      => '?',
                    'width'       => '?',
                    'filesize'    => $phraseaResult['source']['size'],
                    'devices'     => [
                        'all'
                    ],
                    'player_type' => 'IMAGE',
                    'mime_type'   => $phraseaResult['source']['type'],
                    'substituted' => false,
                    'created_on'  => $phraseaResult['source']['createdAt'],
                    'updated_on'  => $phraseaResult['source']['updatedAt'],
                    'url'         => $phraseaResult['source']['url'],
                    'url_ttl'     => '?',
                ],
                [
                    'name'        => 'thumbnail',
                    'permalink'   => [
                        'created_on'   => '?',
                        'id'           => '?',
                        'is_activated' => true,
                        'label'        => $phraseaResult['title'],
                        'updated_on'   => '?',
                        'page_url'     => '?',
                        'download_url' => '?',
                        'url'          => $phraseaResult['thumbnail']['file']['url'],
                    ],
                    'height'      => '?',
                    'width'       => '?',
                    'filesize'    => $phraseaResult['thumbnail']['file']['size'],
                    'devices'     => [
                        'screen'
                    ],
                    'player_type' => 'IMAGE',
                    'mime_type'   => $phraseaResult['thumbnail']['file']['type'],
                    'substituted' => false,
                    'created_on'  => '?',
                    'updated_on'  => '?',
                    'url'         => $phraseaResult['thumbnail']['file']['url'],
                    'url_ttl'     => '?',
                ],
                [
                    'name'        => 'preview',
                    'permalink'   => [
                        'created_on'   => '?',
                        'id'           => '?',
                        'is_activated' => true,
                        'label'        => $phraseaResult['title'],
                        'updated_on'   => '?',
                        'page_url'     => '?',
                        'download_url' => '?',
                        'url'          => $phraseaResult['preview']['file']['url'],
                    ],
                    'height'      => '?',
                    'width'       => '?',
                    'filesize'    => $phraseaResult['preview']['file']['size'],
                    'devices'     => [
                        'screen'
                    ],
                    'player_type' => 'IMAGE',
                    'mime_type'   => $phraseaResult['preview']['file']['type'],
                    'substituted' => false,
                    'created_on'  => '?',
                    'updated_on'  => '?',
                    'url'         => $phraseaResult['preview']['file']['url'],
                    'url_ttl'     => '?',
                ],
            ],
            'metadata'               => [],
            'status'                 => [],
            'caption'                => [],
        ];
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export($response, true)), FILE_APPEND);

        // turn array into object
        $response = json_decode(json_encode($response));

        return \Alchemy\Phraseanet\PhraseanetSDK\Entity\Record::fromValue($response);
    }

    /**
     * Find records
     *
     * @param integer $offsetStart The offset
     * @param integer $perPage     The number of item per page
     * @return ArrayCollection
     * @throws RuntimeException
     */
    public function find($offsetStart, $perPage)
    {
        $response = $this->query('POST', 'v1/records/search/', [], [
            'query'        => 'all',
            'offset_start' => (int)$offsetStart,
            'per_page'     => (int)$perPage,
        ]);

        if (true !== $response->hasProperty('results')) {
            throw new RuntimeException('Missing "results" property in response content');
        }

        return new ArrayCollection(\Alchemy\Phraseanet\PhraseanetSDK\Entity\Record::fromList(
            $response->getProperty('results')
        ));
    }

    /**
     * Search for records
     *
     * @param array $parameters Query parameters
     * @param int $pAPINumber   API number (e.g. 3)
     * @return \Alchemy\Phraseanet\PhraseanetSDK\Entity\Query object
     * @throws RuntimeException
     */
    public function search(array $parameters = [], $pAPINumber = 1)
    {
        //  file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true)), FILE_APPEND);
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export($parameters, true)), FILE_APPEND);

        $page = 1;
        $response = $this->query(
            'GET',
            '/assets',
            [
                'page'    => $page,
                'parents' => $parameters['bases'],
                'query'   => $parameters['query']
            ],
            [],
            [
                'Accept' => 'application/ld+json'
            ]
        );


        if ($response->isEmpty()) {
            throw new RuntimeException('Response content is empty');
        }

        $res = $response->getResult();
//        $results = new \stdClass();
//        $results->results = new \stdClass();
//        foreach ($res->results as $key => $r) {
//            $results->results->records[$key] = $r->_source;
//        }
//
//        if (!isset($results->results->records)) {
//            $results->results->records = [];
//        }
//        $results->results->stories = [];
//        $results->facets = $res->facets;
//        $results->count = $res->count;
//        $results->total = $res->total;
//        $results->limit = isset($res->limit) ? $res->limit : 10;  // TODO: just $res->limit after a phraseanet PR in searchraw
//        $results->offset = isset($res->offset) ? $res->offset : 0;  // TODO: just $res->offset after a phraseanet PR

        $results = [
            'results' => [
                'count'   => count($res['hydra:member']),
                'total'   => $res['hydra:totalItems'],
                'limit'   => 50,
                'offset'  => ($page - 1) * 50,
                'facets'  => [],
                'stories' => [],
                'records' => array_map(function ($asset) {
                    return [
                        'record_id'       => $asset['id'],
                        'collection_id'   => $asset['referenceCollection']['id'],
                        'original_name'   => '?',
                        'mime'            => $asset['source']['type'],
                        'type'            => '?',
                        'cover_record_id' => null,
                        'created_on'      => $asset['createdAt'],
                        'updated_on'      => $asset['updatedAt'],
                        'coll_id'         => $asset['referenceCollection']['id'],
                        'collection_name' => $asset['referenceCollection']['title'],
                        'width'           => '?',
                        'height'          => '?',
                        'size'            => $asset['source']['size'],
                        'base_id'         => $asset['referenceCollection']['id'],
                        'databox_id'      => $asset['workspace']['id'],
                        'databox_name'    => $asset['workspace']['name'],
                        'record_type'     => 'record',
                        'title'           => $asset['title'],
                        'caption'         => [],
                        'caption_all'     => [],
                        'metadata_tags'   => [],
                        'flags'           => [],
                        'subdefs'         => [
                            'document'  => [
                                'width'     => '?',
                                'height'    => '?',
                                'size'      => $asset['source']['size'],
                                'mime'      => $asset['source']['type'],
                                'permalink' => $asset['source']['url'],
                            ],
                            'thumbnail' => [
                                'width'     => '?',
                                'height'    => '?',
                                'size'      => $asset['thumbnail']['file']['size'],
                                'mime'      => $asset['thumbnail']['file']['type'],
                                'permalink' => $asset['thumbnail']['file']['url'],
                            ],
                        ]
                    ];
                }, $res['hydra:member']),
            ],
        ];
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export($results, true)), FILE_APPEND);

        // turn array into object
        $results = json_decode(json_encode($results));

        return Query::fromValue($this->em, $results);
    }
}
