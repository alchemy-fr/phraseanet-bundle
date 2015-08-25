<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\ApplicationTokenProvider;

class ApplicationTokenProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testGetTokenReturnsAssignedToken()
    {
        $token = uniqid('bacon');
        $provider = new ApplicationTokenProvider($token);

        $this->assertEquals($token, $provider->getToken());
    }
}
