<?php

namespace Alchemy\Phraseanet\Predicate;

class PredicateBuilder
{

    /**
     * @var \SplStack
     */
    private $predicateStack;

    /**
     * @var Predicate
     */
    private $predicate = null;

    public function __construct()
    {
        $this->predicateStack = new \SplStack();
        $this->predicate = new NullPredicate();
    }

    public function where($expression)
    {
        if (! $expression instanceof Predicate) {
            $expression = new LiteralPredicate($expression);
        }

        $this->predicateStack = new \SplStack();
        $this->predicate = $expression;

        return $this;
    }

    public function andWhere($expression)
    {
        if (! $expression instanceof Predicate) {
            $expression = new LiteralPredicate($expression);
        }

        $this->predicate = $this->predicate->andPredicate($expression);

        return $this;
    }

    public function startAndGroup()
    {
        $predicate = new NullPredicate();

        $this->predicateStack->push($this->predicate->andPredicate($predicate));
        $this->predicate = $predicate;

        return $this;
    }

    public function orWhere($expression)
    {
        if (! $expression instanceof Predicate) {
            $expression = new LiteralPredicate($expression);
        }

        $this->predicate = $this->predicate->orPredicate($expression);

        return $this;
    }

    public function startOrGroup()
    {
        $predicate = new NullPredicate();

        $this->predicateStack->push($this->predicate->orPredicate($predicate));
        $this->predicate = $predicate;

        return $this;
    }

    public function endGroup()
    {
        if ($this->predicateStack->isEmpty()) {
            throw new \BadMethodCallException('Invalid operation: Not in a condition group.');
        }

        $predicate = $this->predicateStack->pop();
        $predicate->pushPredicate($this->predicate);
        $this->predicate = $predicate;
    }

    public function endAllGroups()
    {
        while (! $this->predicateStack->isEmpty()) {
            $this->endGroup();
        }
    }

    public function getPredicate()
    {
        $this->endAllGroups();

        return $this->predicate;
    }
}
