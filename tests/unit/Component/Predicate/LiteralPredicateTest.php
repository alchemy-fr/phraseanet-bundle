<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\Predicate\LiteralPredicate;
use Alchemy\Phraseanet\Predicate\PredicateVisitor;
use Prophecy\Argument;

class LiteralPredicateTest extends \PHPUnit_Framework_TestCase
{

    public function testGetPredicateValueReturnsLiteralValue()
    {
        $value = 'test value';
        $predicate = new LiteralPredicate($value);

        $this->assertEquals($value, $predicate->getPredicateValue());
    }

    public function testAcceptVisitorDispatchesCallToVisitLiteralPredicateMethod()
    {
        $value = 'test value';
        $predicate = new LiteralPredicate($value);

        $visitor = $this->prophesize(PredicateVisitor::class);
        $visitor->visitLiteralPredicate(Argument::exact($predicate))->shouldBeCalled();

        $predicate->acceptPredicateVisitor($visitor->reveal());
    }
}
