<?php

namespace Alchemy\Phraseanet\Predicate;

class NullPredicate extends AbstractPredicate
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
    }
}
