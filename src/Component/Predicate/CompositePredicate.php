<?php

namespace Alchemy\Phraseanet\Predicate;

use Traversable;

abstract class CompositePredicate extends AbstractPredicate implements \IteratorAggregate
{

    private $predicates = array();

    /**
     * @param Predicate $predicate
     * @return Predicate
     */
    public function pushPredicate(Predicate $predicate)
    {
        $this->predicates[] = $predicate;

        return $this;
    }

    /**
     * @return Predicate[]
     */
    public function getPredicates()
    {
        return $this->predicates;
    }

    public function pruneInstancesOf($class, $recursive = false)
    {
        $pruned = [];

        foreach ($this->predicates as $predicate) {
            if ($predicate instanceof $class) {
                continue;
            }

            if ($recursive && $predicate instanceof self) {
                $predicate = $predicate->pruneInstancesOf($class, $recursive);
            }

            $pruned[] = $predicate;
        }

        $clone = clone $this;
        $clone->predicates = $pruned;

        return $clone;
    }

    public function pruneRedundantComposites($recursive = false)
    {
        if (empty($this->predicates)) {
            return new NullPredicate();
        }

        $pruned = [];

        foreach ($this->predicates as $predicate) {
            if ($recursive && $predicate instanceof self) {
                $predicate = $predicate->pruneRedundantComposites($recursive);
            }

            $pruned[] = $predicate;
        }

        $clone = clone $this;
        $clone->predicates = $pruned;

        if (count($clone->predicates) == 1) {
            return reset($clone->predicates);
        }

        return $clone;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getPredicates());
    }
}
