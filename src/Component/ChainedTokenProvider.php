<?php

namespace Alchemy\Phraseanet;

class ChainedTokenProvider implements TokenProvider
{

    /**
     * @var TokenProvider
     */
    private $defaultProvider;

    /**
     * @var TokenProvider[]
     */
    private $providers;

    public function addProvider(TokenProvider $provider)
    {
        $this->providers[] = $provider;
    }

    public function setDefaultProvider(TokenProvider $provider)
    {
        $this->defaultProvider = $provider;
    }

    /**
     * @return null|string
     */
    public function getToken()
    {
        foreach ($this->providers as $provider) {
            $token = $provider->getToken();

            if ($token) {
                return $token;
            }
        }

        if ($this->defaultProvider !== null) {
            return $this->defaultProvider->getToken();
        }

        return null;
    }
}
