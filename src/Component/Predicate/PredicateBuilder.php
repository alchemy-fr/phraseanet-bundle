<?php

namespace Alchemy\Phraseanet\Predicate;

class PredicateBuilder
{

    /**
     * @var \SplStack
     */
    private $predicateStack;

    public function __construct()
    {
        $this->predicateStack = new \SplStack();
        $this->predicateStack->push(new AndPredicate(new NullPredicate()));
    }

    public function where($expression)
    {
        if (! $expression instanceof Predicate) {
            $expression = new LiteralPredicate($expression);
        }

        $this->predicateStack = new \SplStack();
        $this->predicateStack->push(new AndPredicate($expression));

        return $this;
    }

    public function andWhere($expression)
    {
        if (! $expression instanceof Predicate) {
            $expression = new LiteralPredicate($expression);
        }

        if ($this->predicateStack->count() > 1  && ! $this->predicateStack->top() instanceof AndPredicate) {
            $this->startAndGroup();
        }

        $predicate = $this->predicateStack->pop()->andPredicate($expression);
        $this->predicateStack->push($predicate);

        return $this;
    }

    public function orWhere($expression)
    {
        if (! $expression instanceof Predicate) {
            $expression = new LiteralPredicate($expression);
        }

        if ($this->predicateStack->count() > 1  && ! $this->predicateStack->top() instanceof OrPredicate) {
            $this->startOrGroup();
        }

        $predicate = $this->predicateStack->pop()->orPredicate($expression);
        $this->predicateStack->push($predicate);

        return $this;
    }

    public function startAndGroup()
    {
        $predicate = new AndPredicate(new NullPredicate());

        $this->predicateStack->top()->pushPredicate($predicate);
        $this->predicateStack->push($predicate);

        return $this;
    }

    public function startOrGroup()
    {
        $predicate = new OrPredicate(new NullPredicate());

        $this->predicateStack->top()->pushPredicate($predicate);
        $this->predicateStack->push($predicate);

        return $this;
    }

    public function endGroup()
    {
        if ($this->predicateStack->count() <= 1) {
            throw new \BadMethodCallException('Invalid operation: Not in a condition group.');
        }

        $this->predicateStack->pop();

        return $this;
    }

    public function getPredicate()
    {
        $predicate = $this->predicateStack->bottom()
            ->pruneInstancesOf(NullPredicate::class, true)
            ->pruneRedundantComposites(true);

        if ($predicate instanceof CompositePredicate) {
            $predicate = $predicate->pruneInstancesOf(NullPredicate::class, true);
        }

        return $predicate;
    }
}
