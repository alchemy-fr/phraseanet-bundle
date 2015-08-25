<?php

namespace Alchemy\Phraseanet\Tests\Predicate;

use Alchemy\Phraseanet\Predicate\LiteralPredicate;
use Alchemy\Phraseanet\Predicate\PredicateBuilder;

class PredicateBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testWhereBuildsSingleLiteralPredicate()
    {
        $builder = new PredicateBuilder();

        $builder->where('bacon');

        $this->assertInstanceOf(LiteralPredicate::class, $builder->getPredicate());
        $this->assertEquals('bacon', $builder->getPredicate()->getPredicateValue());
    }
}
