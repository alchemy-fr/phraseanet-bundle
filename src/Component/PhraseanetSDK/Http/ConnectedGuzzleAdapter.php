<?php

/*
 * This file is part of Phraseanet SDK.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phraseanet\PhraseanetSDK\Http;

class ConnectedGuzzleAdapter implements GuzzleAdapterInterface
{
    /** @var GuzzleAdapterInterface */
    private $adapter;
    private $token;

    public function __construct($token, GuzzleAdapterInterface $adapter)
    {
        $this->token = $token;
        $this->adapter = $adapter;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    public function getGuzzle()
    {
        return $this->adapter->getGuzzle();
    }

    public function call(
        $method,
        $path,
        array $query = array(),
        array $postFields = array(),
        array $files = array(),
        array $headers = array()
    ) {
//        $query = array_replace($query, array(
//            'oauth_token' => $this->token,
//        ));
        $headers['Authorization'] = 'Bearer ' . $this->token;
//        $headers['Accept'] = 'application/json';

//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
//        file_put_contents("/var/parade/log.txt",
//            sprintf("method=%s\npath=%s\nquery=%s\npostFields=%s\nheaders=%s\n",
//                $method,
//                $path,
//                var_export($query, true),
//                var_export($postFields, true),
//                var_export($headers, true)
//            ), FILE_APPEND);

        $r = $this->adapter->call($method, $path, $query, $postFields, $files, $headers);
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export($r, true)), FILE_APPEND);
        return $r;
    }
}
