<?php

namespace Alchemy\Phraseanet\Tests;

use Alchemy\Phraseanet\Predicate\AndPredicate;
use Alchemy\Phraseanet\Predicate\LiteralPredicate;
use Alchemy\Phraseanet\Predicate\NullPredicate;
use Alchemy\Phraseanet\Predicate\OrPredicate;
use Alchemy\Phraseanet\Predicate\PredicateVisitor;

class NullPredicateTest extends \PHPUnit_Framework_TestCase
{

    public function testAcceptVisitorDoesNotDispatch()
    {
        $predicate = new NullPredicate();
        $visitor = new NullPredicateVisitor($this);

        $predicate->acceptPredicateVisitor($visitor);
    }
}

class NullPredicateVisitor implements PredicateVisitor
{
    /**
     * @var NullPredicateTest
     */
    private $test;

    public function __construct(NullPredicateTest $test)
    {
        $this->test = $test;
    }

    public function visitAndPredicate(AndPredicate $predicate)
    {
        $this->test->fail(__METHOD__ . ' should not be called');
    }

    public function visitOrPredicate(OrPredicate $predicate)
    {
        $this->test->fail(__METHOD__ . ' should not be called');
    }

    public function visitLiteralPredicate(LiteralPredicate $predicate)
    {
        $this->test->fail(__METHOD__ . ' should not be called');
    }
}
