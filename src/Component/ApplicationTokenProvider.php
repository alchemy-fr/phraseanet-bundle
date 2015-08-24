<?php

namespace Alchemy\Phraseanet;

class ApplicationTokenProvider implements TokenProvider
{

    /**
     * @var string
     */
    private $token;

    /**
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }
}
