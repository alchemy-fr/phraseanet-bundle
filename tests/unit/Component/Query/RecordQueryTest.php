<?php

namespace Alchemy\Phraseanet\Tests\Query;

use Alchemy\Phraseanet\Phraseanet\Query\RecordQuery;

class RecordQueryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetQueryReturnsCorrectValue()
    {
        $query = new RecordQuery(array('beacon' => true), 1);

        $this->assertEquals(array('beacon' => true), $query->getRawQuery());
    }
}
