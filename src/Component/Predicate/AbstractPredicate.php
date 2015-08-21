<?php

namespace Alchemy\Phraseanet\Predicate;

abstract class AbstractPredicate implements Predicate
{

    /**
     * Performs a boolean and composition with another predicate
     *
     * @param Predicate $predicate
     * @return Predicate
     */
    public function andPredicate(Predicate $predicate)
    {
        return new AndPredicate($this, $predicate);
    }

    /**
     * Performs a boolean or composition with another predicate
     *
     * @param Predicate $predicate
     * @return Predicate
     */
    public function orPredicate(Predicate $predicate)
    {
        return new OrPredicate($this, $predicate);
    }
}
