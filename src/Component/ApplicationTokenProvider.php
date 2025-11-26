<?php

namespace Alchemy\Phraseanet;

use Alchemy\Phraseanet\PhraseanetSDK\Application;

class ApplicationTokenProvider implements TokenProvider
{

    /**
     * @var string
     */
    private $token;
    private $application;
    private $phrasea_client_id;
    private $phrasea_secret;

    /**
     * @param string $token
     * @param string $phrasea_client_id
     * @param string $phraea_secret
     * @param Application $application
     */
    public function __construct($token, $phrasea_client_id, $phrasea_secret, $application)
    {
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n%s\n%s\n", __FILE__, __LINE__, __FUNCTION__, Application::shortToken($token), $phrasea_client_id, $phrasea_secret), FILE_APPEND);
        // $this->token = $token;
        $this->application = $application;
        $this->phrasea_client_id = $phrasea_client_id;
        $this->phrasea_secret = $phrasea_secret;
    }

    /**
     * @return string
     */
    public function getToken()
    {
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, Application::shortToken($this->token)), FILE_APPEND);
        if(!$this->token) {
            $baseUrl = $this->application->getAdapter()->getBaseUrl();
            $adapter = $this->application->getAdapter();
            $guzzle = $adapter->getGuzzle();

            $url = $baseUrl . '/oauth/v2/token';
//            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, $url), FILE_APPEND);
            $body = json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $this->phrasea_client_id, // "parade_dev_jy"
                'client_secret' => $this->phrasea_secret, // "VA02cFsJrA4WQExRMt8iicelVUYcNIHQ"
                //          'scope' => 'super-admin',
            ]);
            $request = $guzzle->createRequest(
                'POST',
                $url,
                [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Content-Length' => strlen($body),
                ],
                $body,
                []
            );
            $response = $request->send();
//            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, $response->getBody(true)), FILE_APPEND);

            $tokenData = json_decode($response->getBody(true), true);
            $this->token = $tokenData['access_token'];

//            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, Application::shortToken($this->token)), FILE_APPEND);
        }
        return $this->token;
    }

}
