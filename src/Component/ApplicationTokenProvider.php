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
        if(!$this->token) {
            $baseUrl = $this->application->getAdapter()->getBaseUrl();
            $adapter = $this->application->getAdapter();
            $guzzle = $adapter->getGuzzle();

            $url = $baseUrl . '/oauth/v2/token';
            $body = json_encode([
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->secret,
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

            $tokenData = json_decode($response->getBody(true), true);
            $this->token = $tokenData['access_token'];
        }

        return $this->token;
    }

}
