<?php

namespace Alchemy\Phraseanet\Tests\Query;

use Alchemy\Phraseanet\Predicate\LiteralPredicate;
use Alchemy\Phraseanet\Predicate\NullPredicate;
use Alchemy\Phraseanet\Query\QueryPredicateVisitor;

class QueryPredicateVisitorTest extends \PHPUnit_Framework_TestCase
{

    public function testCompileLiteralPredicate()
    {
        $predicate = new LiteralPredicate('bacon');
        $compiler = new QueryPredicateVisitor();

        $this->assertEquals('bacon', $compiler->compile($predicate));
    }

    public function testCompileAndPredicate()
    {
        $predicate = new LiteralPredicate('bacon');
        $predicate = $predicate->andPredicate(new LiteralPredicate('eggs'));

        $compiler = new QueryPredicateVisitor();

        $this->assertEquals('(bacon AND eggs)', $compiler->compile($predicate));
    }

    public function testCompileOrPredicate()
    {
        $predicate = new LiteralPredicate('bacon');
        $predicate = $predicate->orPredicate(new LiteralPredicate('eggs'));

        $compiler = new QueryPredicateVisitor();

        $this->assertEquals('(bacon OR eggs)', $compiler->compile($predicate));
    }

    public function testCompileCompositePredicateWithNullValues()
    {
        $predicate = new LiteralPredicate('bacon');
        $andPredicate = $predicate->andPredicate(new NullPredicate());
        $orPredicate = $predicate->orPredicate(new NullPredicate());

        $compiler = new QueryPredicateVisitor();

        $this->assertEquals('bacon', $compiler->compile($andPredicate));
        $this->assertEquals('bacon', $compiler->compile($orPredicate));
    }

    public function testCompileMultipleExpressionsInAndPredicate()
    {
        $predicate = new LiteralPredicate('bacon');
        $predicate = $predicate->andPredicate(new LiteralPredicate('eggs'));
        $predicate = $predicate->andPredicate(new LiteralPredicate('steak'));

        $compiler =  new QueryPredicateVisitor();

        $this->assertEquals('(bacon AND eggs AND steak)', $compiler->compile($predicate));
    }

    public function testCompileMultipleExpressionsInOrPredicate()
    {
        $predicate = new LiteralPredicate('bacon');
        $predicate = $predicate->orPredicate(new LiteralPredicate('eggs'));
        $predicate = $predicate->orPredicate(new LiteralPredicate('steak'));

        $compiler =  new QueryPredicateVisitor();

        $this->assertEquals('(bacon OR eggs OR steak)', $compiler->compile($predicate));
    }

    public function testCompileMultipleComposites()
    {
        $predicate = new LiteralPredicate('bacon');
        $predicate = $predicate->andPredicate(new LiteralPredicate('eggs'));
        $predicate = $predicate->orPredicate(new LiteralPredicate('steak'));

        $compiler = new QueryPredicateVisitor();

        $this->assertEquals('((bacon AND eggs) OR steak)', $compiler->compile($predicate));

        $predicate = new LiteralPredicate('bacon');
        $predicate = $predicate->orPredicate(new LiteralPredicate('eggs'));
        $predicate = $predicate->andPredicate(new LiteralPredicate('steak'));

        $this->assertEquals('((bacon OR eggs) AND steak)', $compiler->compile($predicate));
    }
}
