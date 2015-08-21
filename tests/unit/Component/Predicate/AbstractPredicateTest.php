<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\Predicate\AbstractPredicate;
use Alchemy\Phraseanet\Predicate\AndPredicate;
use Alchemy\Phraseanet\Predicate\NullPredicate;
use Alchemy\Phraseanet\Predicate\OrPredicate;
use Alchemy\Phraseanet\Predicate\PredicateVisitor;

class AbstractPredicateTest extends \PHPUnit_Framework_TestCase
{
    public function testAndPredicateReturnsNewAndPredicate()
    {
        $predicate = new TestableAbstractPredicate();

        $andPredicate = $predicate->andPredicate(new NullPredicate());

        $this->assertInstanceOf(AndPredicate::class, $andPredicate);
        $this->assertCount(2, $andPredicate);
    }

    public function testOrPredicateReturnsNewOrPredicate()
    {
        $predicate = new TestableAbstractPredicate();

        $orPredicate = $predicate->orPredicate(new NullPredicate());

        $this->assertInstanceOf(orPredicate::class, $orPredicate);
        $this->assertCount(2, $orPredicate);
    }
}

class TestableAbstractPredicate extends AbstractPredicate
{
    /**
     * Accepts a predicate visitor.
     * Implementations should dispatch the call to the
     * appropriate PredicateVisitor::visit*() method.
     *
     * @param PredicateVisitor $visitor
     */
    public function acceptPredicateVisitor(PredicateVisitor $visitor)
    {
        // No op, for testing only
    }
}
