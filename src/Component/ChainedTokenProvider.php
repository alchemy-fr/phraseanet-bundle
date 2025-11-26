<?php

namespace Alchemy\Phraseanet;

use Alchemy\Phraseanet\PhraseanetSDK\Application;

class ChainedTokenProvider implements TokenProvider
{

    /**
     * @var TokenProvider
     */
    private $defaultProvider;

    /**
     * @var TokenProvider[]
     */
    private $providers = array();

    public function addProvider(TokenProvider $provider)
    {
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
        $this->providers[] = $provider;
    }

    public function setDefaultProvider(TokenProvider $provider)
    {
//        file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n", __FILE__, __LINE__, __FUNCTION__), FILE_APPEND);
        $this->defaultProvider = $provider;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        foreach ($this->providers as $provider) {
            $token = $provider->getToken();
//            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, Application::shortToken($this->defaultProvider->getToken())), FILE_APPEND);

            if ($token) {
                return $token;
            }
        }

        if ($this->defaultProvider !== null) {
//            file_put_contents("/var/parade/log.txt", sprintf("%s:%d %s(...)\n%s\n", __FILE__, __LINE__, __FUNCTION__, Application::shortToken($this->defaultProvider->getToken())), FILE_APPEND);
            return $this->defaultProvider->getToken();
        }

        return null;
    }
}
