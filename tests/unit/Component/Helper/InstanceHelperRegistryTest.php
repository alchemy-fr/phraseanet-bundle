<?php

namespace Alchemy\Phraseanet\Tests\Helper;

use Alchemy\Phraseanet\Helper\InstanceHelper;
use Alchemy\Phraseanet\Helper\InstanceHelperRegistry;

class InstanceHelperRegistryTest extends \PHPUnit_Framework_TestCase
{

    public function testHasHelperReturnsFalseForUndefinedNames()
    {
        $helperRegistry = new InstanceHelperRegistry();

        $this->assertFalse($helperRegistry->hasHelper('missing'));
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testGetUndefinedHelperThrowsOutOfBoundsException()
    {
        $helperRegistry = new InstanceHelperRegistry();

        $helperRegistry->getHelper('missing');
    }

    public function testGetDefinedHelperReturnsHelper()
    {
        $helper = $this->prophesize(InstanceHelper::class);
        $helperRegistry = new InstanceHelperRegistry();

        $helperRegistry->addHelper('helper', $helper->reveal());

        $this->assertSame($helper->reveal(), $helperRegistry->getHelper('helper'));
    }

    public function testGetDefaultHelperReturnsFirstHelperWhenDefaultIsNotSet()
    {
        $helperA = $this->prophesize(InstanceHelper::class);
        $helperB = $this->prophesize(InstanceHelper::class);
        $helperRegistry = new InstanceHelperRegistry();

        $helperRegistry->addHelper('helperA', $helperA->reveal());
        $helperRegistry->addHelper('helperB', $helperB->reveal());

        $this->assertSame($helperA->reveal(), $helperRegistry->getDefaultHelper());
    }

    public function testGetDefaultHelperReturnsDefinedDefaultHelper()
    {
        $helperA = $this->prophesize(InstanceHelper::class);
        $helperB = $this->prophesize(InstanceHelper::class);
        $helperRegistry = new InstanceHelperRegistry();

        $helperRegistry->addHelper('helperA', $helperA->reveal());
        $helperRegistry->addHelper('helperB', $helperB->reveal());

        $helperRegistry->setDefaultHelper('helperB');

        $this->assertSame($helperB->reveal(), $helperRegistry->getDefaultHelper());
    }
}
