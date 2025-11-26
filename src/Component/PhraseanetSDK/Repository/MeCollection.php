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
use Alchemy\Phraseanet\PhraseanetSDK\Exception\NotFoundException;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\RuntimeException;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\UnauthorizedException;
use Alchemy\Phraseanet\PhraseanetSDK\Entity\MeCollection as MeCollectionEntity;

class MeCollection extends AbstractRepository
{
	/**
	 * Return all collections available
	 *
	 * @return MeCollection[]
	 * @throws NotFoundException
	 * @throws UnauthorizedException
	 */
	public function getCollectionsList()
	{
        static $r = null;
//		$response = $this->query('GET', 'v1/me/collections/');
//
//		if ($response->hasProperty(('collections')) !== true) {
//			throw new RuntimeException('Missing "collections" property in response content');
//		}
//
//        return MeCollectionEntity::fromList($response->getProperty('collections'));
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
        if($r === null) {
            $response = $this->query(
                'GET',
                '/collections',
                [],
                [],
                [
                    'Accept' => 'application/ld+json'
                ]
            );
            $res = $response->getResult();
// file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export($res, true)), FILE_APPEND);
            $collections = [];
            foreach ($res['hydra:member'] as $collection) {
                $collections[] = [
                    'databox_id'    => $collection['workspace']['id'],
                    'base_id'       => $collection['id'],
                    'collection_id' => $collection['id'],
                    'name'          => $collection['title'],
                    'logo'          => '?',
                    'labels'        => [],
                    'rights'        => '?',
                    'statuses'      => '?',
                ];
                foreach ($collection['children'] as $child) {
                    $collections[] = [
                        'databox_id'    => $collection['workspace']['id'],
                        'base_id'       => $child['id'],
                        'collection_id' => $child['id'],
                        'name'          => $child['title'],
                        'logo'          => '?',
                        'labels'        => [],
                        'rights'        => '?',
                        'statuses'      => '?',
                    ];
                }
            }
file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export($collections, true)), FILE_APPEND);

            // turn array into object
            $collections = json_decode(json_encode($collections));

            $r = MeCollectionEntity::fromList($collections);
        }
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export($r, true)), FILE_APPEND);
        return $r;
	}
}
