<?php

namespace Alchemy\Phraseanet\Tests\Predicate;

use Alchemy\Phraseanet\Predicate\CompositePredicate;
use Alchemy\Phraseanet\Predicate\LiteralPredicate;
use Alchemy\Phraseanet\Predicate\NullPredicate;
use Alchemy\Phraseanet\Predicate\PredicateVisitor;

class CompositePredicateTest extends \PHPUnit_Framework_TestCase
{

    public function testPruningNodesByType()
    {
        $predicate = new StubCompositePredicate();

        $predicate->pushPredicate(new LiteralPredicate('test'));
        $predicate->pushPredicate(new NullPredicate());
        $predicate->pushPredicate(new LiteralPredicate('test 2'));

        $this->assertCount(2, $predicate->pruneInstancesOf(NullPredicate::class));
        $this->assertCount(1, $predicate->pruneInstancesOf(LiteralPredicate::class));
    }

    public function testRecursivelyPruningNodesByType()
    {
        $predicate = new StubCompositePredicate();
        $nested = new StubCompositePredicate();

        $nested->pushPredicate(new LiteralPredicate('test'));
        $nested->pushPredicate(new NullPredicate());
        $nested->pushPredicate(new LiteralPredicate('test 2'));

        $predicate->pushPredicate(new NullPredicate());
        $predicate->pushPredicate($nested);

        $this->assertCount(1, $predicate->pruneInstancesOf(NullPredicate::class, true));

        $predicate = $predicate->pruneInstancesOf(NullPredicate::class, true);

        $this->assertCount(2, $predicate->getPredicates()[0]->getPredicates());
    }

    public function testPruningRedundantNodesOnEmptyCompositeReturnsNullPredicate()
    {
        $predicate = new StubCompositePredicate();

        $this->assertInstanceOf(NullPredicate::class, $predicate->pruneRedundantComposites());
    }

    public function testRecursivelyPruningRedundantNodesOnEmptyCompositeReturnsNullPredicate()
    {
        $predicate = new StubCompositePredicate();
        $predicate->pushPredicate(new StubCompositePredicate());

        $this->assertInstanceOf(NullPredicate::class, $predicate->pruneRedundantComposites(true));

        $predicate = new StubCompositePredicate();

        $child = new StubCompositePredicate();
        $child->pushPredicate(new StubCompositePredicate());

        $predicate->pushPredicate($child);

        $this->assertInstanceOf(NullPredicate::class, $predicate->pruneRedundantComposites(true));
    }


    public function testRecursivelyPruningRedundantNodesOnCompositeChainReturnsDeepestPredicate()
    {
        $predicate = new StubCompositePredicate();
        $child = new StubCompositePredicate();

        $child->pushPredicate(new LiteralPredicate('test'));

        $predicate->pushPredicate($child);

        $this->assertInstanceOf(LiteralPredicate::class, $predicate->pruneRedundantComposites(true));
    }

    public function testPruningRedundantNodesOnCompositeWithSingleChildReturnsChildPredicate()
    {
        $child = new LiteralPredicate('test');
        $predicate = new StubCompositePredicate();

        $predicate->pushPredicate($child);

        $this->assertSame($child, $predicate->pruneRedundantComposites());
    }


}

class StubCompositePredicate extends CompositePredicate
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
        // TODO: Implement acceptPredicateVisitor() method.
    }
}
