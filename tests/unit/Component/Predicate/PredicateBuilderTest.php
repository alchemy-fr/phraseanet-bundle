<?php

namespace Alchemy\Phraseanet\Tests\Predicate;

use Alchemy\Phraseanet\Predicate\AndPredicate;
use Alchemy\Phraseanet\Predicate\LiteralPredicate;
use Alchemy\Phraseanet\Predicate\OrPredicate;
use Alchemy\Phraseanet\Predicate\PredicateBuilder;
use Alchemy\Phraseanet\Query\QueryPredicateVisitor;

class PredicateBuilderTest extends \PHPUnit_Framework_TestCase
{

    public function testWhereBuildsSingleLiteralPredicate()
    {
        $builder = new PredicateBuilder();

        $builder->where('bacon');

        $this->assertInstanceOf(LiteralPredicate::class, $builder->getPredicate());
        $this->assertEquals('bacon', $builder->getPredicate()->getPredicateValue());
    }

    public function testWhereAndWhereBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->where('bacon');
        $builder->andWhere('eggs');

        $this->assertInstanceOf(AndPredicate::class, $builder->getPredicate());

        $predicates = $builder->getPredicate()->getPredicates();

        $this->assertInstanceOf(LiteralPredicate::class, $predicates[0]);
        $this->assertInstanceOf(LiteralPredicate::class, $predicates[1]);

        $this->assertEquals('bacon', $predicates[0]->getPredicateValue());
        $this->assertEquals('eggs', $predicates[1]->getPredicateValue());
    }

    public function testWhereOrWhereBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->where('bacon');
        $builder->orWhere('eggs');

        $this->assertInstanceOf(OrPredicate::class, $builder->getPredicate());

        $predicates = $builder->getPredicate()->getPredicates();

        $this->assertInstanceOf(LiteralPredicate::class, $predicates[0]);
        $this->assertInstanceOf(LiteralPredicate::class, $predicates[1]);

        $this->assertEquals('bacon', $predicates[0]->getPredicateValue());
        $this->assertEquals('eggs', $predicates[1]->getPredicateValue());
    }

    public function testStartAndGroupBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->startAndGroup();
        $builder->orWhere('bacon');
        $builder->orWhere('eggs');
        $builder->endGroup();
        $builder->andWhere('test');

        $predicate = $builder->getPredicate();

        $this->assertInstanceOf(AndPredicate::class, $predicate);

        $predicates = $predicate->getPredicates();

        $this->assertCount(2, $predicates);
        $this->assertInstanceOf(OrPredicate::class, $predicates[0]);
        $this->assertCount(2, $predicates[0]->getPredicates());

        $predicates = $predicates[0]->getPredicates();

        $this->assertInstanceOf(LiteralPredicate::class, $predicates[0]);
        $this->assertInstanceOf(LiteralPredicate::class, $predicates[1]);
        $this->assertEquals('bacon', $predicates[0]->getPredicateValue());
        $this->assertEquals('eggs', $predicates[1]->getPredicateValue());
    }


    public function testStartOrGroupBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->startOrGroup();
        $builder->andWhere('bacon');
        $builder->andWhere('eggs');
        $builder->endGroup();
        $builder->orWhere('test');

        $predicate = $builder->getPredicate();

        $this->assertInstanceOf(OrPredicate::class, $predicate);

        $predicates = $predicate->getPredicates();

        $this->assertCount(2, $predicates);
        $this->assertInstanceOf(AndPredicate::class, $predicates[0]);
        $this->assertCount(2, $predicates[0]->getPredicates());

        $predicates = $predicates[0]->getPredicates();

        $this->assertInstanceOf(LiteralPredicate::class, $predicates[0]);
        $this->assertInstanceOf(LiteralPredicate::class, $predicates[1]);
        $this->assertEquals('bacon', $predicates[0]->getPredicateValue());
        $this->assertEquals('eggs', $predicates[1]->getPredicateValue());
    }

    public function testNestingGroupsBuildsCorrectCompositeStructure()
    {
        $builder = new PredicateBuilder();

        $builder->startOrGroup();
        $builder->startAndGroup();
        $builder->andWhere('bacon');
        $builder->andWhere('eggs');
        $builder->endGroup();
        $builder->startAndGroup();
        $builder->andWhere('steak');
        $builder->andWhere('fries');
        $builder->endGroup();
        $builder->endGroup();

        $compiler = new QueryPredicateVisitor();
        $query = $compiler->compile($builder->getPredicate());

        $this->assertEquals('((bacon AND eggs) OR (steak AND fries))', $query);
    }
}
