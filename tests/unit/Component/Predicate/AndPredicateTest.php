<?php

namespace Alchemy\Phraseanet\Tests\Predicate;

use Alchemy\Phraseanet\Predicate\AndPredicate;
use Alchemy\Phraseanet\Predicate\NullPredicate;
use Alchemy\Phraseanet\Predicate\PredicateVisitor;
use Prophecy\Argument;

class AndPredicateTest extends \PHPUnit_Framework_TestCase
{

    public function testAndPredicateReturnsSelfWithExtraPredicate()
    {
        $predicate = new AndPredicate(new NullPredicate(), new NullPredicate());

        $andPredicate = $predicate->andPredicate($predicate);

        $this->assertSame($predicate, $andPredicate);
        $this->assertCount(3, $andPredicate);
    }

    public function testAcceptVisitorDispatchesToVisitAndPredicateMethod()
    {
        $predicate = new AndPredicate(new NullPredicate(), new NullPredicate());
        $visitor = $this->prophesize(PredicateVisitor::class);

        $visitor->visitAndPredicate(Argument::exact($predicate))->shouldBeCalled();

        $predicate->acceptPredicateVisitor($visitor->reveal());
    }
}
