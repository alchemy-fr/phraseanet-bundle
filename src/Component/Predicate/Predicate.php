<?php

namespace Alchemy\Phraseanet\Predicate;

/**
 * Interface for boolean predicates.
 *
 * @author thibaud
 */
interface Predicate
{
    /**
     * Performs a boolean and composition with another predicate
     *
     * @param Predicate $predicate
     * @return Predicate
     */
    public function andPredicate(Predicate $predicate);

    /**
     * Performs a boolean or composition with another predicate
     *
     * @param Predicate $predicate
     * @return Predicate
     */
    public function orPredicate(Predicate $predicate);

    /**
     * Accepts a predicate visitor.
     * Implementations should dispatch the call to the
     * appropriate PredicateVisitor::visit*() method.
     *
     * @param PredicateVisitor $visitor
     */
    public function acceptPredicateVisitor(PredicateVisitor $visitor);
}
