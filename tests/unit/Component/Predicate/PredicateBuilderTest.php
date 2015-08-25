<?php

namespace Alchemy\Phraseanet\Tests\Predicate;

use Alchemy\Phraseanet\Predicate\PredicateBuilder;
use Alchemy\Phraseanet\Query\QueryPredicateVisitor;

class PredicateBuilderTest extends \PHPUnit_Framework_TestCase
{

    private function assertQuery($expected, PredicateBuilder $builder)
    {
        $compiler = new QueryPredicateVisitor();

        $this->assertEquals($expected, $compiler->compile($builder->getPredicate()));
    }

    public function testWhereBuildsSingleLiteralPredicate()
    {
        $builder = new PredicateBuilder();

        $builder->where('bacon');

        $this->assertQuery('bacon', $builder);
    }

    public function testWhereAndWhereBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->where('bacon');
        $builder->andWhere('eggs');

        $this->assertQuery('(bacon AND eggs)', $builder);
    }

    public function testWhereOrWhereBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->where('bacon');
        $builder->orWhere('eggs');

        $this->assertQuery('(bacon OR eggs)', $builder);
    }

    public function testStartAndGroupBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->startOrGroup();
        $builder->orWhere('bacon');
        $builder->orWhere('eggs');
        $builder->endGroup();
        $builder->where('steak');

        $this->assertQuery('((bacon OR eggs) AND steak)', $builder);
    }

    public function testStartOrGroupBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->startOrGroup();
        $builder->andWhere('bacon');
        $builder->andWhere('eggs');
        $builder->endGroup();
        $builder->orWhere('steak');

        $this->assertQuery('((bacon AND eggs) OR steak)', $builder);
    }

    public function testStartMultipleOrGroupsBuildsCompositePredicate()
    {
        $builder = new PredicateBuilder();

        $builder->startOrGroup();
        $builder->startOrGroup();
        $builder->orWhere('bacon');
        $builder->orWhere('eggs');
        $builder->endGroup();
        $builder->where('steak');
        $builder->endGroup();
        $builder->where('wine');

        $this->assertQuery('(((bacon OR eggs) OR steak) AND wine)', $builder);
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
