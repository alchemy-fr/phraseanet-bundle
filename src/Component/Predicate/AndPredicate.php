<?php

namespace Alchemy\Phraseanet\Predicate;

class AndPredicate extends CompositePredicate
{

    public function __construct(Predicate $lhs, Predicate $rhs = null)
    {
        $this->pushPredicate($lhs);

        if ($rhs !== null) {
            $this->pushPredicate($rhs);
        }
    }

    public function andPredicate(Predicate $predicate)
    {
        return $this->pushPredicate($predicate);
    }

    /**
     * Accepts a predicate visitor.
     * Implementations should dispatch the call to the
     * appropriate PredicateVisitor::visit*() method.
     *
     * @param PredicateVisitor $visitor
     */
    public function acceptPredicateVisitor(PredicateVisitor $visitor)
    {
        return $visitor->visitAndPredicate($this);
    }
}
