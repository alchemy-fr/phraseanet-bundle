<?php

namespace Alchemy\Phraseanet\Predicate;

interface PredicateVisitor
{

    public function visitAndPredicate(AndPredicate $predicate);

    public function visitOrPredicate(OrPredicate $predicate);

    public function visitLiteralPredicate(LiteralPredicate $predicate);
}
