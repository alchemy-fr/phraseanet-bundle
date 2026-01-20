<?php

namespace Alchemy\Phraseanet\PhraseanetSDK\Repository;

use Alchemy\Phraseanet\PhraseanetSDK\AbstractRepository;
use Alchemy\Phraseanet\PhraseanetSDK\Entity\Query;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\RuntimeException;
use DateTime;
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

        $asset = $response->getResult();

        $metadata = [];
        $metadataByStruct_id = [];

        foreach ($asset['attributes'] as $attribute) {
            $locale = array_key_exists('locale', $attribute) ? strtoupper($attribute['locale']) : '';
            $struct_id = $attribute['definition']['id'] . '_' . $locale;
            $name = str_replace(' ', '', ucwords($attribute['definition']['name'])) . $locale;
            $metadata[] = [
                'meta_structure_id' => $struct_id,
                'name'              => $name,
                'labels'            => [],
                'meta_id'           => $attribute['id'],
                'value'             => $attribute['value'],
            ];
            if (!array_key_exists($struct_id, $metadataByStruct_id)) {
                $metadataByStruct_id[$struct_id] = [
                    'meta_structure_id' => $struct_id,
                    'name'              => $name,
                    'value'             => [],
                ];
            }
            $metadataByStruct_id[$struct_id]['value'][] = $attribute['value'];
        }

        $subdefs = [];
        if(array_key_exists('main', $asset)) {
            $mime_type = $asset['source']['type'];
            $player_type = strtoupper((explode('/', $mime_type))[0]);
            $subdefs['document'] = [
                    'name'        => 'document',
                    'permalink'   => [
                        'created_on'   => $asset['source']['createdAt'],
                        'id'           => $asset['source']['id'],
                        'is_activated' => true,
                        'label'        => $asset['title'],
                        'updated_on'   => $asset['source']['updatedAt'],
                        'page_url'     => $asset['source']['id'],
                        'download_url' => '?',
                        'url'          => $asset['source']['url'],
                    ],
                    'height'      => '?',
                    'width'       => '?',
                    'filesize'    => $asset['source']['size'],
                    'devices'     => [
                        'all'
                    ],
                    // todo phrasea: determine player_type from mime_type
                    'player_type' => $player_type,
                    'mime_type'   => $mime_type,
                    'substituted' => false,
                    'created_on'  => $asset['source']['createdAt'],
                    'updated_on'  => $asset['source']['updatedAt'],
                    'url'         => $asset['source']['url'],
                    'url_ttl'     => '?',
            ];
        }
        foreach(['preview' , 'thumbnail'] as $assetFile) {
            if(!isset($asset[$assetFile])) {
                continue;
            }
            $mime_type = $asset[$assetFile]['file']['type'];
            $player_type = strtoupper((explode('/', $mime_type))[0]);
            $subdefs[$assetFile] = [
                'name'        => $assetFile,
                'permalink'   => [
                    'created_on'   => '?',
                    'id'           => '?',
                    'is_activated' => true,
                    'label'        => $asset['title'],
                    'updated_on'   => '?',
                    'page_url'     => '?',
                    'download_url' => '?',
                    'url'          => $asset[$assetFile]['file']['url'],
                ],
                'height'      => '?',
                'width'       => '?',
                'filesize'    => $asset[$assetFile]['file']['size'],
                'devices'     => [
                    'screen'
                ],
                // todo phrasea: determine player_type from mime_type
                'player_type' => $player_type,
                'mime_type'   => $mime_type,
                'substituted' => false,
                'created_on'  => '?',
                'updated_on'  => '?',
                'url'         => $asset[$assetFile]['file']['url'],
                'url_ttl'     => '?',
            ];
        }

        $mime_type = $asset['source']['type'] ?? null;
        $phrasea_type = $mime_type ? (explode('/', ($mime_type)))[0] : null;
        $response = [
            'databox_id'             => $asset['workspace']['id'],
            'record_id'              => $asset['id'],
            'resource_id'            => $asset['id'],
            'mime_type'              => $mime_type,
            'mime'                   => $mime_type,
            'title'                  => $asset['title'],
            'original_name'          => '?',
            'updated_on'             => $asset['updatedAt'],
            'created_on'             => $asset['createdAt'],
            'collection_id'          => $asset['referenceCollection']['id'],
            'base_id'                => $asset['referenceCollection']['id'],
            'sha256'                 => '?',
            'thumbnail'              => $subdefs['thumbnail'] ?? null,
            'technical_informations' => [
                [ 'name' => 'Channels', 'value' => '?' ],
                [ 'name' => 'ColorDepth', 'value' => '?' ],
                [ 'name' => 'ColorSpace', 'value' => '?' ],
                [ 'name' => 'FileSize', 'value' => $asset['source']['size'] ?? 0 ],
                [ 'name' => 'Height', 'value' => '?' ],
                [ 'name' => 'MimeType', 'value' => $mime_type ],
                [ 'name' => 'Width', 'value' => '?' ],
            ],
            'phrasea_type'           => $phrasea_type,
            'type'                   => $phrasea_type,
            'uuid'                   => '?',
            'subdefs'                => array_values($subdefs),
            'metadata'               => $metadata,
            'status'                 => [],
            'caption'                => array_values(array_map(function ($attribute) {
                return [
                    'meta_structure_id'   => $attribute['meta_structure_id'],
                    'name' => $attribute['name'],
                    'value'           => join(' ; ', $attribute['value']),
                ];
            }, $metadataByStruct_id)),
        ];


        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s...\n", __FILE__, __LINE__, __FUNCTION__, substr(var_export($response, true), 0, 100)), FILE_APPEND);

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
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(..., pAPINumber=%s)\n%s\n", __FILE__, __LINE__, __FUNCTION__, $pAPINumber, var_export($parameters, true)), FILE_APPEND);
        if(0) {
            $response = $this->query('POST', 'v'.$pAPINumber.'/searchraw/', array(), array_merge(
                array('search_type' => 0),
                $parameters
            ));

            if ($response->isEmpty()) {
                throw new RuntimeException('Response content is empty');
            }

            $results = $res = $response->getResult();
            if ($pAPINumber == 3) {
                $results = new \stdClass();
                $results->results = new \stdClass();
                foreach ($res->results as $key => $r) {
                    $results->results->records[$key] = $r->_source;
                }

                if (!isset($results->results->records)) {
                    $results->results->records = [];
                }

                $results->results->stories = [];
                $results->facets = $res->facets;
                $results->count = $res->count;
                $results->total = $res->total;
                $results->limit = isset($res->limit) ? $res->limit : 10;  // TODO: just $res->limit after a phraseanet PR in searchraw
                $results->offset = isset($res->offset) ? $res->offset : 0;  // TODO: just $res->offset after a phraseanet PR
                return Query::fromValue($this->em, $results);
            }
        }
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);

        $limit = $parameters['limit'] ?? 10;
        $offset = $parameters['offset'] ?? 0;
        $page = (int)($offset / $limit) + 1;
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
        $conditions = ['@isStory = false'];
        // $conditions[] = 'crash > "2026-01-30"';
        if(array_key_exists('date_field', $parameters)) {
            if(array_key_exists('date_min', $parameters)) {
                $date_min = new \DateTime($parameters['date_min']);
                $conditions[] = sprintf('%s >= "%s"', $parameters['date_field'], $date_min->format(DateTime::ATOM));
            }
            if(array_key_exists('date_max', $parameters)) {
                $date_max = new \DateTime($parameters['date_max']);
                $conditions[] = sprintf('%s <= "%s"', $parameters['date_field'], $date_max->format(DateTime::ATOM));
            }
        }
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\nconditions = %s\n", __FILE__, __LINE__, __FUNCTION__, var_export($conditions, true)), FILE_APPEND);

        $response = $this->query(
            'GET',
            '/assets',
            [
                'page'    => $page,
                'limit'   => $limit,
                'parents' => $parameters['bases'],
                'query'   => $parameters['query'],
                'conditions' => $conditions,
            ],
            [],
            [
                'Accept' => 'application/ld+json',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Cache-Control' => 'no-cache',
            ]
        );
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);

        if ($response->isEmpty()) {
            throw new RuntimeException('Response content is empty');
        }

        $res = $response->getResult();
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n\n", __FILE__, __LINE__, __FUNCTION__, var_export($res, true)), FILE_APPEND);

        $results = [
            'results' => [
                'stories' => [],
                'records' => array_map(function ($asset) {
                    $caption = [];
                    $caption_all = [];
                    foreach ($asset['attributes'] as $attribute) {
                        if($attribute['value'] === null || $attribute['value'] === '') {
                            continue;
                        }
                        $locale = array_key_exists('locale', $attribute) ? strtoupper($attribute['locale']) : '';
                        $name =  str_replace(' ', '', ucwords($attribute['definition']['name'])) . $locale;
                        if (!array_key_exists($name, $caption)) {
                            $caption[$name] = [];
                        }
                        $caption[$name][] = $attribute['value'];
                        $caption_all[] = $attribute['value'];
                    }

                    $subdefs = [];
                    foreach(['main' => 'document', 'preview' => 'preview', 'thumbnail' => 'thumbnail'] as $assetFile => $subdefName) {
                        if(!isset($asset[$assetFile])) {
                            continue;
                        }
                        $subdefs[$subdefName] = [
                            'width'     => '?',
                            'height'    => '?',
                            'size'      => $asset[$assetFile]['file']['size'],
                            'mime'      => $asset[$assetFile]['file']['type'],
                            'permalink' => $asset[$assetFile]['file']['url'],
                        ];
                    }

                    // todo phrasea: do we need all tags ? (requires GET /tags call)
                    $flags = [];
                    foreach ($asset['tags'] ?? [] as $tag) {
                        $flags[$tag['name']] = true;
                    }

                    $mime_type = $asset['source']['type'] ?? null;
                    $phrasea_type = $mime_type ? (explode('/', ($mime_type)))[0] : null;
                    return [
                        'record_id'       => $asset['id'],
                        'collection_id'   => $asset['referenceCollection']['id'],
                        'uuid'            => '?',
                        'flags_bitfield'  => 0,
                        'sha256'          => '?',
                        'original_name'   => '?',
                        'mime'            => $mime_type,
                        'type'            => $phrasea_type,
                        'cover_record_id' => null,
                        'created_on'      => $asset['createdAt'],
                        'updated_on'      => $asset['updatedAt'],
                        'coll_id'         => $asset['referenceCollection']['id'],
                        'collection_name' => $asset['referenceCollection']['title'],
                        'width'           => 0,
                        'height'          => 0,
                        'size'            => $asset['source']['size'] ?? null,
                        'base_id'         => $asset['referenceCollection']['id'],
                        'databox_id'      => $asset['workspace']['id'],
                        'databox_name'    => $asset['workspace']['name'],
                        'record_type'     => 'record',
                        'title'           => $asset['title'],
                        'caption'         => $caption,
                        'caption_all'     => join("\n", $caption_all),
                        'metadata_tags'   => [
                            "Aperture" => 0,
                            "CameraModel" => "?",
                            "Channels" => 0,
//                            "ColorDepth": 8,
//                            "ColorSpace": 0,
                            "FileSize" => $asset['source']['size'] ?? 0,
//                           "FlashFired": false,
//                            "FocalLength": 35,
//                            "Height": 4000,
//                            "HyperfocalDistance": 5.8866667511817,
//                            "ISO": 100,
//                            "LightValue": 14.562719427049,
//                            "MimeType": "image/jpeg",
//                            "Orientation": 0,
//                            "ShutterSpeed": 0.005,
//                            "Width": 6000,
//                            "ThumbnailOrientation": "L"
                        ],
                        'flags'           => $flags,
                        'subdefs'         => $subdefs
                    ];
                }, $res['hydra:member']),
            ],
            'count'   => count($res['hydra:member']),
            'took'    => 0,
            'total'   => $res['hydra:totalItems'],
            'facets'  => array_values(array_map(function ($k, $facet) {
                return [
                    'name'   => $k,
                    'field' => $facet['meta']['title'],
                    'values' => array_map(function ($bucket) use ($k, $facet) {
                        $v = $bucket['key_as_string'] ?? $bucket['key'];
                        $rv = $bucket['key'];
                        return [
                            'value' => is_array($v) ? $v['label'] : $v,
                            'raw_value' => is_array($rv) ? $rv['value'] : $rv,
                            'count' => $bucket['doc_count'],
                            'query' => $facet['meta']['title'] . '="' . (is_array($rv) ? $rv['value'] : $rv) . '"',
                            // 'query' => $k . '="' . (is_array($rv) ? $rv['value'] : $rv) . '"',
                        ];
                    }, $facet['buckets'] ?? []),
                ];
            }, array_keys($res['facets'] ?? []), array_values($res['facets'] ?? []))),
            'offset'  => ($page - 1) * $limit,
            'limit'   => $limit,

        ];
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n\n", __FILE__, __LINE__, __FUNCTION__, substr(var_export($results, true), 0, 200000)), FILE_APPEND);

        // turn array into object
        $results = json_decode(json_encode($results));

        return Query::fromValue($this->em, $results);
    }
}
