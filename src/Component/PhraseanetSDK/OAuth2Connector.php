<?php

namespace Alchemy\Phraseanet\PhraseanetSDK;

use Alchemy\Phraseanet\PhraseanetSDK\Http\GuzzleAdapter;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\AuthenticationException;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\BadResponseException;

class OAuth2Connector
{
    const TOKEN_ENDPOINT = '/api/oauthv2/token';
    const AUTH_ENDPOINT = '/api/oauthv2/authorize';

    /**
     * Oauth authorization grant type
     */
    const GRANT_TYPE_AUTHORIZATION = 'authorization_code';

    /**
     * @var GuzzleAdapter
     */
    private $adapter;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $secret;

    /**
     * @param GuzzleAdapter $adapter
     * @param string $clientId
     * @param string $secret
     */
    public function __construct(GuzzleAdapter $adapter, $clientId, $secret)
    {
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(%s, %s, %s)\n", __FILE__, __LINE__, __FUNCTION__, $adapter->getBaseUrl(), $clientId, $secret), FILE_APPEND);
        $this->adapter = $adapter;
        $this->clientId = $clientId;
        $this->secret = $secret;
    }

    private function getUrl()
    {
        $baseUrl = $this->adapter->getBaseUrl();

        return substr($baseUrl, 0, strlen($baseUrl) - 8);
    }

    /**
     * Builds the Authorization Url
     *
     * @param string $redirectUri
     * @param array $parameters
     * @param array $scopes
     *
     * @return string
     */
    public function getAuthorizationUrl($redirectUri, array $parameters = array(), array $scopes = array())
    {
        $oauthParams = array_replace($parameters, array(
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'client_id' => $this->clientId,
            'scope' => implode(' ', $scopes),
        ));

        $parameters = http_build_query($oauthParams, null, '&');

        return sprintf('%s%s?%s', $this->getUrl(), static::AUTH_ENDPOINT, $parameters);
    }

    /**
     * Retrieves your access token from your callback endpoint
     *
     * @param $code
     * @param $redirectUri
     *
     * @return string
     *
     * @throws AuthenticationException
     */
    public function retrieveAccessToken($code, $redirectUri)
    {
        $postFields = array(
            'grant_type' => static::GRANT_TYPE_AUTHORIZATION,
            'redirect_uri' => $redirectUri,
            'client_id' => $this->clientId,
            'client_secret' => $this->secret,
            'code' => $code,
        );
        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);

        try {
            $responseContent = $this->adapter->call(
                'POST',
                $this->getUrl() . static::TOKEN_ENDPOINT,
                array(),
                $postFields
            );
            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
            $data = json_decode($responseContent, true);
            $token = $data["access_token"];
            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...) ---> token=%s\n", __FILE__, __LINE__, __FUNCTION__, Application::shortToken($token)), FILE_APPEND);
        } catch (BadResponseException $e) {
            $response = json_decode($e->getResponseBody(), true);
            $msg = isset($response['error']) ? $response['error'] : (isset($response['msg']) ? $response['msg'] : '');

            throw new AuthenticationException($msg);
        }

        return $token;
    }
}
