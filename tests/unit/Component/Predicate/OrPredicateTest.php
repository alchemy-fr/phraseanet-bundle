<?php

namespace Alchemy\Phraseanet\Tests\Predicate;

use Alchemy\Phraseanet\Predicate\NullPredicate;
use Alchemy\Phraseanet\Predicate\OrPredicate;
use Alchemy\Phraseanet\Predicate\PredicateVisitor;
use Prophecy\Argument;

class OrPredicateTest extends \PHPUnit_Framework_TestCase
{

    public function testOrPredicateReturnsSelfWithExtraPredicate()
    {
        $predicate = new OrPredicate(new NullPredicate(), new NullPredicate());

        $orPredicate = $predicate->orPredicate(new NullPredicate());

        $this->assertSame($predicate, $orPredicate);
        $this->assertCount(3, $orPredicate);
    }

    public function testAcceptVisitorDispatchesToVisitOrPredicateMethod()
    {
        $predicate = new OrPredicate(new NullPredicate(), new NullPredicate());
        $visitor = $this->prophesize(PredicateVisitor::class);

        $visitor->visitOrPredicate(Argument::exact($predicate))->shouldBeCalled();

        $predicate->acceptPredicateVisitor($visitor->reveal());
    }
}
