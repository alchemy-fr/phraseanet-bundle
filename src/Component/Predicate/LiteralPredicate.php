<?php

namespace Alchemy\Phraseanet\Predicate;

class LiteralPredicate extends AbstractPredicate
{
    /**
     * @var string
     */
    private $predicateValue = '';

    /**
     * @param string $predicateValue A literal predicate expression.
     */
    public function __construct($predicateValue)
    {
        $this->predicateValue = $predicateValue;
    }

    /**
     * @return string
     */
    public function getPredicateValue()
    {
        return $this->predicateValue;
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
        return $visitor->visitLiteralPredicate($this);
    }
}
