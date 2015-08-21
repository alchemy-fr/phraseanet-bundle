<?php

namespace Alchemy\Phraseanet\Tests\Query;

use Alchemy\Phraseanet\Query\RecordQuery;

class RecordQueryTest extends \PHPUnit_Framework_TestCase
{

    public function testGetQueryReturnsCorrectValue()
    {
        $query = new RecordQuery(array('beacon' => true), 1);

        $this->assertEquals(array('beacon' => true), $query->getRawQuery());
        $this->assertEquals(1, $query->getQueryType());
    }

    public function getInvalidRepositories()
    {
        return array(
            array(new \stdClass()),
            array('test'),
            array(true),
            array(null)
        );
    }

    /**
     * @dataProvider getInvalidRepositories
     * @expectedException \InvalidArgumentException
     */
    public function testExecutingQueryWithAnInvalidRepositoryRaisesAnException($repository)
    {
        $query = new RecordQuery([], 0);

        $query->execute($repository);
    }
}
