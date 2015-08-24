<?php

namespace Alchemy\Phraseanet\Query;

use Alchemy\Phraseanet\Predicate\AndPredicate;
use Alchemy\Phraseanet\Predicate\OrPredicate;
use Alchemy\Phraseanet\Predicate\Predicate;
use Alchemy\Phraseanet\Predicate\PredicateVisitor;
use Alchemy\Phraseanet\Predicate\LiteralPredicate;

class QueryPredicateVisitor implements PredicateVisitor
{
    /**
     * @var null|string
     */
    private $expression = null;

    /**
     * @param Predicate $predicate
     * @param string $defaultValue
     * @return string
     */
    public function compile(Predicate $predicate, $defaultValue = '')
    {
        $this->expression = '';

        $predicate->acceptPredicateVisitor($this);

        return $this->expression ?: $defaultValue;
    }

    /**
     * @param AndPredicate $predicate
     * @return string
     */
    public function visitAndPredicate(AndPredicate $predicate)
    {
        $this->expression = $this->merge(' AND ', $this->compilePredicates($predicate));
    }

    /**
     * @param OrPredicate $predicate
     * @return string
     */
    public function visitOrPredicate(OrPredicate $predicate)
    {
        $this->expression = $this->merge(' OR ', $this->compilePredicates($predicate));
    }

    /**
     * @param string $glue
     * @param string[] $expressions
     * @return string
     */
    private function merge($glue, array $expressions)
    {
        if (count($expressions) == 1) {
            return reset($expressions);
        }

        $expression = implode($glue, $expressions);

        if (trim($expression) !== '') {
            $expression = sprintf('(%s)', $expression);
        }

        return $expression;
    }

    /**
     * @param \Traversable $predicates
     * @return string[]
     */
    private function compilePredicates(\Traversable $predicates)
    {
        $expressions = [];

        foreach ($predicates as $childPredicate) {
            $expressions[] = $this->compile($childPredicate);
        }

        return $this->filterEmptyExpressions($expressions);
    }

    /**
     * @param array $expressions
     * @return array
     */
    private function filterEmptyExpressions(array $expressions)
    {
        return array_filter($expressions, function ($expression) {
            return $expression && trim($expression) != '';
        });
    }

    /**
     * @param LiteralPredicate $predicate
     */
    public function visitLiteralPredicate(LiteralPredicate $predicate)
    {
        $this->expression = $predicate->getPredicateValue();
    }
}
