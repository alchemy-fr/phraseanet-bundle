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
