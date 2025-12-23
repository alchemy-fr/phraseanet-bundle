<?php

/*
 * This file is part of Phraseanet SDK.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phraseanet\PhraseanetSDK\Repository;

use Alchemy\Phraseanet\PhraseanetSDK\AbstractRepository;
use Alchemy\Phraseanet\PhraseanetSDK\Entity\Query;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\RuntimeException;
use Doctrine\Common\Collections\ArrayCollection;
use Alchemy\Phraseanet\PhraseanetSDK\Search\SearchResult;

class Story extends AbstractRepository
{
    /**
     * Find the story by its id that belongs to the provided databox
     *
     * @param  integer $databoxId The record databox id
     * @param  integer $recordId  The record id
     * @return Alchemy\Phraseanet\PhraseanetSDK\Entity\Story
     * @throws RuntimeException
     */
    public function findById($databoxId, $recordId)
    {
        $path = sprintf('v1/stories/%s/%s/', $databoxId, $recordId);

        $response = $this->query('GET', $path);

        if (true !== $response->hasProperty('story')) {
            throw new RuntimeException('Missing "story" property in response content');
        }

        return \Alchemy\Phraseanet\PhraseanetSDK\Entity\Story::fromValue($this->em, $response->getProperty('story'));
    }

    /**
     * Find stories
     *
     * @param  integer $offsetStart The offset
     * @param  integer $perPage The number of item per page
     * @return ArrayCollection|Story[]
     * @throws RuntimeException
     */
    public function find($offsetStart, $perPage)
    {
        $response = $this->query('POST', 'v1/search/', array(), array(
            'query'        => '',
            'search_type'  => SearchResult::TYPE_STORY,
            'offset_start' => (int) $offsetStart,
            'per_page'     => (int) $perPage,
        ));

        if (true !== $response->hasProperty('results')) {
            throw new RuntimeException('Missing "results" property in response content');
        }

        return Query::fromValue($this->em, $response->getResult())->getResults()->getStories();
    }

    /**
     * Search for stories
     *
     * @param  array $parameters Query parameters
	 * @param int $pAPINumber API number (e.g. 3)
     * @return \Alchemy\Phraseanet\PhraseanetSDK\Entity\Query object
     * @throws RuntimeException
     */
    public function search(array $parameters = array(), $pAPINumber = 1)
    {
        if(0) {
            $response = $this->query('POST', 'v' . $pAPINumber . '/searchraw/', [], array_merge(
                $parameters,
                ['search_type' => SearchResult::TYPE_STORY]
            ));

            if ($response->isEmpty()) {
                throw new RuntimeException('Response content is empty');
            }

            $results = $res = $response->getResult();
            if ($pAPINumber == 3) {
                $results = new \stdClass();
                $results->results = new \stdClass();
                foreach ($res->results as $key => $r) {
                    $results->results->stories[$key] = $r->_source;
                }

                if (!isset($results->results->stories)) {
                    $results->results->stories = [];
                }

                $results->results->records = [];
                $results->facets = $res->facets;
            }

            return Query::fromValue($this->em, $results);
        }

        $limit = isset($res->limit) ? $res->limit : 10;
        $offset = isset($res->offset) ? $res->offset : 0;
        $page = (int)($offset / $limit) + 1;        // todo phrasea : check this calculation is correct
        $response = $this->query(
            'GET',
            '/assets',
            [
                'page'    => $page,
                'limit'   => $limit,
                'parents' => $parameters['bases'],
                'query'   => $parameters['query'],
                'conditions[]' => '@isStory=true',
            ],
            [],
            [
                'Accept' => 'application/ld+json',
                // todo phrasea: get right cookie name (depends on conf) ?
                'Accept-Language' => $_COOKIE['parade-standard-ml-lng'] ?? 'en',
            ]
        );

        if ($response->isEmpty()) {
            throw new RuntimeException('Response content is empty');
        }

        $res = $response->getResult();
        $results = [
            'results' => [
                'stories' => [],
                'records' => array_map(function ($asset) {
                    $caption = [];
                    $caption_all = [];
                    foreach ($asset['attributes'] as $attribute) {
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

                    $phrasea_type = (explode('/', $asset['source']['type']))[0];
                    return [
                        'record_id'       => $asset['id'],
                        'collection_id'   => $asset['referenceCollection']['id'],
                        'uuid'            => '?',
                        'flags_bitfield'  => 0,
                        'sha256'          => '?',
                        'original_name'   => '?',
                        'mime'            => $asset['source']['type'],
                        'type'            => $phrasea_type,
                        'cover_record_id' => null,
                        'created_on'      => $asset['createdAt'],
                        'updated_on'      => $asset['updatedAt'],
                        'coll_id'         => $asset['referenceCollection']['id'],
                        'collection_name' => $asset['referenceCollection']['title'],
                        'width'           => 0,
                        'height'          => 0,
                        'size'            => $asset['source']['size'],
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
                            "FileSize" => $asset['source']['size'],
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
                    'values' => array_map(function ($bucket) use ($k) {
                        $v = $bucket['key_as_string'] ?? $bucket['key'];
                        $rv = $bucket['key'];
                        return [
                            'value' => is_array($v) ? $v['label'] : $v,
                            'raw_value' => is_array($rv) ? $rv['value'] : $rv,
                            'count' => $bucket['doc_count'],
                            'query' => $k . '="' . (is_array($rv) ? $rv['value'] : $rv) . '"',
                        ];
                    }, $facet['buckets'] ?? []),
                ];
            }, array_keys($res['facets'] ?? []), array_values($res['facets'] ?? []))),
            'offset'  => ($page - 1) * $limit,
            'limit'   => $limit,

        ];
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s...\n\n", __FILE__, __LINE__, __FUNCTION__, substr(var_export($results, true), 0, 200000)), FILE_APPEND);

        // turn array into object
        $results = json_decode(json_encode($results));

        return Query::fromValue($this->em, $results);


    }
}
