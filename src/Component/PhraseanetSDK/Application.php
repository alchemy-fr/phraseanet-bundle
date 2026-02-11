<?php

/*
 * This file is part of Phraseanet SDK.
 *
 * (c) Alchemy <info@alchemy.fr>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Alchemy\Phraseanet\PhraseanetSDK;

use Alchemy\Phraseanet\PhraseanetSDK\ApplicationInterface;
use Alchemy\Phraseanet\PhraseanetSDK\EntityManager;
use Alchemy\Phraseanet\PhraseanetSDK\Http\GuzzleAdapter;
use Alchemy\Phraseanet\PhraseanetSDK\Exception\InvalidArgumentException;
use Alchemy\Phraseanet\PhraseanetSDK\Http\ConnectedGuzzleAdapter;
use Alchemy\Phraseanet\PhraseanetSDK\Http\APIGuzzleAdapter;
use Alchemy\Phraseanet\PhraseanetSDK\Monitor;
use Alchemy\Phraseanet\PhraseanetSDK\OAuth2Connector;
use Alchemy\Phraseanet\PhraseanetSDK\Uploader;
use Psr\Log\NullLogger;

/**
 * Phraseanet SDK Application
 */
class Application implements ApplicationInterface
{
    /**
     * Creates the application.
     *
     * @param array $config
     * @param GuzzleAdapter $adapter
     * @return Application
     */
    public static function create(array $config, GuzzleAdapter $adapter = null)
    {
        foreach (array('client_id', 'secret') as $key) {
            if (!isset($config[$key]) || !is_string($config[$key])) {
                throw new InvalidArgumentException(sprintf('Missing or invalid parameter "%s"', $key));
            }
        }

        if (null === $adapter) {
            if (!isset($config['url']) || !is_string($config['url'])) {
                throw new InvalidArgumentException(sprintf('Missing or invalid parameter "url"'));
            }

            $adapter = GuzzleAdapter::create($config['url']);
        }

        return new static(
            $adapter,
            $config['client_id'],
            $config['secret']
        );
    }

    /**
     * @param $token
     */
    private static function assertValidToken($token)
    {
        if ('' === trim($token)) {
            throw new InvalidArgumentException('Token can not be empty.');
        }
    }

    /**
     * @var GuzzleAdapter
     */
    private $adapter;

    /**
     * @var string Application client ID. Used by Oauth2Connector
     */
    private $clientId;

    /**
     * @var string Application secret. Used by Oauth2Connector
     */
    private $secret;

    /**
     * @var OAuth2Connector
     */
    private $connector;

    /**
     * @var EntityManager[]
     */
    private $entityManagers = array();

    /**
     * @var APIGuzzleAdapter[]
     */
    private $adapters = array();

    /**
     * @var Uploader[]
     */
    private $uploaders = array();

    /**
     * @var Monitor[]
     */
    private $monitors = array();

    public function __construct(GuzzleAdapter $adapter, $clientId, $secret)
    {
  //      file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, var_export(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true)), FILE_APPEND);
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, $adapter->getBaseUrl()), FILE_APPEND);
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
        $this->adapter = $adapter;
        $this->clientId = $clientId;
        $this->secret = $secret;
    }

    public function setSslVerification($sslVerification)
    {
        $this->adapter->setSslVerification($sslVerification);
    }

    /**
     * Activate extended graph object by adding required accept headers.
     * This results in bigger response message but less requests to get
     * relation of queried object.
     *
     * @param $mode
     */
    public function setExtendedMode($mode)
    {
        $this->adapter->setExtended($mode);
    }

    /**
     * {@inheritdoc}
     */
    public function getOauth2Connector()
    {
        if ($this->connector === null) {
            $this->connector = new OAuth2Connector($this->adapter, $this->clientId, $this->secret);
        }

        return $this->connector;
    }

     /**
     * {@inheritdoc}
     */
    public function getUploader($token)
    {
        self::assertValidToken($token);

        if (!isset($this->uploaders[$token])) {
            $this->uploaders[$token] = new Uploader($this->getAdapterByToken($token), $this->getEntityManager($token));
        }

        return $this->uploaders[$token];
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityManager($token)
    {
        self::assertValidToken($token);

        if (!isset($this->entityManagers[$token])) {
            $this->entityManagers[$token] = new EntityManager(
                $this->getAdapterByToken($token),
                new NullLogger()
            );
        }

        return $this->entityManagers[$token];
    }

    /**
     * {@inheritdoc}
     */
    public function getMonitor($token)
    {
        self::assertValidToken($token);

        if (!isset($this->monitors[$token])) {
            $this->monitors[$token] = new Monitor($this->getAdapterByToken($token));
        }

        return $this->monitors[$token];
    }

    /**
     * Returns the guzzle adapter
     *
     * @return GuzzleAdapter
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    private function getAdapterByToken($token)
    {
        if (!isset($this->adapters[$token])) {
            $this->adapters[$token] = new APIGuzzleAdapter(
                new ConnectedGuzzleAdapter(
                    $token,
                    $this->adapter
                )
            );
        }

        return $this->adapters[$token];
    }

    public static function shortToken($token) {
        if(null === $token) {
            return 'null';
        }
        return substr($token, 0, 16).'...'.substr($token, -16);
    }

}
