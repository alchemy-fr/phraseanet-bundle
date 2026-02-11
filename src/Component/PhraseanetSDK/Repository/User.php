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
use Alchemy\Phraseanet\PhraseanetSDK\Entity\MeCollection as MeCollectionEntity;
use Alchemy\Phraseanet\PhraseanetSDK\EntityHydrator;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\RuntimeException;
use Alchemy\Phraseanet\PhraseanetSDK\Repository\MeCollection as MeCollectionRepository;

class User extends AbstractRepository
{
    /**
     * @return \Alchemy\Phraseanet\PhraseanetSDK\Entity\User
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\NotFoundException
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\UnauthorizedException
     * @deprecated Use User::me() instead
     */
    public function findMe()
    {
        return $this->me();
    }

    /**
     * @return \Alchemy\Phraseanet\PhraseanetSDK\Entity\User
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\NotFoundException
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\UnauthorizedException
     */
    public function me()
    {
        static $user = null;
        if($user === null) {
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
            $collections = [];
            foreach ($res['hydra:member'] as $collection) {
                $collections[] = [
                    'databox_id'    => $collection['workspace']['id'],
                    'base_id'       => $collection['id'],
                    'collection_id' => $collection['id'],
                    'name'          => $collection['title'],
                    'logo'          => '?',
                    'labels'        => [],
                    'rights'        => [],
                    'statuses'      => [],
                ];
                foreach ($collection['children'] as $child) {
                    $collections[] = [
                        'databox_id'    => $collection['workspace']['id'],
                        'base_id'       => $child['id'],
                        'collection_id' => $child['id'],
                        'name'          => $child['title'],
                        'logo'          => '?',
                        'labels'        => [],
                        'rights'        => [],
                        'statuses'      => [],
                    ];
                }
            }

            // turn array into object
            $collections = json_decode(json_encode($collections));

            $user = [
                '@entity@'        => "http://api.phraseanet.com/api/objects/user",
                'id'              => 1,
                'email'           => "support@alchemy.fr",
                'login'           => "alchemy",
                'first_name'      => "Support",
                'last_name'       => "Alchemy",
                'display_name'    => "Support Alchemy",
                'gender'          => '',
                'address'         => '',
                'zip_code'        => '',
                'city'            => '',
                'country'         => '',
                'phone'           => '',
                'fax'             => '',
                'job'             => '',
                'position'        => '',
                'company'         => '',
                'geoname_id'      => 2988507,
                'last_connection' => "2025-11-24T17:01:54+00:00",
                'created_on'      => "2025-01-04T00:00:00+00:00",
                'updated_on'      => "2025-11-24T17:01:54+00:00",
                'locale'          => "en"
            ];
            // turn array into object
            $user = json_decode(json_encode($user));

            $user = new \Alchemy\Phraseanet\PhraseanetSDK\Entity\User($user);
            $user->setCollectionRights($collections);
        }

        return $user;
    }

    public function requestCollections(array $collections)
    {
        $response = $this->query('POST', 'v1/me/request-collections/', array(), $collections, array(
            'Content-Type' => 'application/json'
        ));

        if (!$response->hasProperty('demands')) {
            throw new RuntimeException('Missing "demands" property in response content');
        }

        return $response->getProperty('demands');
    }

    /**
     * @param $emailAddress
     * @return string
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\NotFoundException
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\UnauthorizedException
     */
    public function requestPasswordReset($emailAddress)
    {
        $response = $this->query('POST', 'v1/accounts/reset-password/' . $emailAddress . '/');

        if (!$response->hasProperty('reset_token')) {
            throw new RuntimeException('Missing "token" property in response content');
        }

        return (string)$response->getProperty('reset_token');
    }

    /**
     * @param $token
     * @param $password
     * @return bool
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\NotFoundException
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\UnauthorizedException
     */
    public function resetPassword($token, $password)
    {
        $response = $this->query('POST', 'v1/accounts/update-password/' . $token . '/', array(), array(
            'password' => $password
        ));

        if (!$response->hasProperty('success')) {
            throw new RuntimeException('Missing "success" property in response content');
        }

        return (bool)$response->getProperty('success');
    }

    public function updatePassword($currentPassword, $newPassword)
    {
        $response = $this->query('POST', 'v1/me/update-password/', array(), array(
            'oldPassword' => $currentPassword,
            'password' => array(
                'password' => $newPassword,
                'confirm' => $newPassword
            )
        ), array('Content-Type' => 'application/json'));

        if (!$response->hasProperty('success')) {
            throw new RuntimeException('Missing "success" property in response content');
        }

        return (bool)$response->getProperty('success');
    }

    /**
     * @param \Alchemy\Phraseanet\PhraseanetSDK\Entity\User $user
     * @param $password
     * @param int[] $collections
     * @return string
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\NotFoundException
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\UnauthorizedException
     */
    public function createUser(\Alchemy\Phraseanet\PhraseanetSDK\Entity\User $user, $password, array $collections = null)
    {
        $data = array(
            'email' => $user->getEmail(),
            'password' => $password,
            'gender' => $user->getGender(),
            'firstname' => $user->getFirstName(),
            'lastname' => $user->getLastName(),
            'city' => $user->getCity(),
            'tel' => $user->getPhone(),
            'company' => $user->getCompany(),
            'job' => $user->getJob(),
            'notifications' => false
        );

        if ($collections !== null) {
            $data['collections'] = $collections;
        }

        $response = $this->query(
            'POST',
            'v1/accounts/access-demand/',
            array(),
            $data,
            array('Content-Type' => 'application/json')
        );

        if (!$response->hasProperty('user')) {
            throw new \RuntimeException('Missing "user" property in response content');
        }

        if (!$response->hasProperty('token')) {
            throw new \RuntimeException('Missing "token" property in response content');
        }

        return (string)$response->getProperty('token');
    }

    public function updateUser(\Alchemy\Phraseanet\PhraseanetSDK\Entity\User $user)
    {
        $data = array(
            'email' => $user->getEmail(),
            'gender' => $user->getGender(),
            'firstname' => $user->getFirstName(),
            'lastname' => $user->getLastName(),
            'city' => $user->getCity(),
            'tel' => $user->getPhone(),
            'company' => $user->getCompany(),
            'job' => $user->getJob(),
            'notifications' => false
        );

        $response = $this->query(
            'POST',
            'v1/me/update-account/',
            array(),
            $data,
            array('Content-Type' => 'application/json')
        );

        if (!$response->hasProperty('success')) {
            throw new RuntimeException('Missing "success" property in response content');
        }

        return (bool)$response->getProperty('success');
    }

    public function deleteAccount()
    {
        $this->query('DELETE', 'me/');
    }

    /**
     * @param $token
     * @return bool
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\NotFoundException
     * @throws \Alchemy\Phraseanet\PhraseanetSDK\Exception\UnauthorizedException
     */
    public function unlockAccount($token)
    {
        $response = $this->query('POST', 'v1/accounts/unlock/' . $token . '/', array(), array());

        if (!$response->hasProperty('success')) {
            throw new \RuntimeException('Missing "success" property in response content');
        }

        return (bool)$response->getProperty('success');
    }
}
