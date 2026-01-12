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
    private $client_id;
    private $secret;

    /**
     * @param string $client_id
     * @param string $secret
     * @param Application $application
     */
    public function __construct($client_id, $secret, $application)
    {
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(client_id='%s', secret='%s', ...)\n", __FILE__, __LINE__, __FUNCTION__, $client_id, $secret), FILE_APPEND);
        $this->application = $application;
        $this->client_id = $client_id;
        $this->secret = $secret;
        $this->token = null;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, Application::shortToken($this->token)), FILE_APPEND);
        if(!$this->token) {
            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
            $baseUrl = $this->application->getAdapter()->getBaseUrl();
            $adapter = $this->application->getAdapter();
            $guzzle = $adapter->getGuzzle();

            $url = $baseUrl . '/oauth/v2/token';
//            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, $url), FILE_APPEND);
            $body = json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->secret,
          //      'scope' => 'super-admin',
                'scope' => 'openid',
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
            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, $response->getBody(true)), FILE_APPEND);

            $tokenData = json_decode($response->getBody(true), true);
            $this->token = $tokenData['access_token'];

            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, Application::shortToken($this->token)), FILE_APPEND);
        }
        return $this->token;
    }

}
