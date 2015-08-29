<?php

namespace Alchemy\Phraseanet\Tests\Helper;

use Alchemy\Phraseanet\Helper\FeedHelper;
use Alchemy\Phraseanet\Helper\InstanceHelper;
use Alchemy\Phraseanet\Helper\MetadataHelper;
use Alchemy\Phraseanet\Helper\ThumbHelper;
use Alchemy\Phraseanet\Mapping\DefinitionMap;
use Alchemy\Phraseanet\Mapping\FieldMap;

class InstanceHelperTest extends \PHPUnit_Framework_TestCase
{

    public function testChildHelpersAreProperlyAssigned()
    {
        $feedHelper = new FeedHelper();
        $metadataHelper = new MetadataHelper(new FieldMap(), 'fr', 'fr');
        $thumbHelper = new ThumbHelper(new DefinitionMap());

        $helper = new InstanceHelper($feedHelper, $metadataHelper, $thumbHelper);

        $this->assertSame($feedHelper, $helper->getFeedHelper());
        $this->assertSame($metadataHelper, $helper->getMetadataHelper());
        $this->assertSame($thumbHelper, $helper->getThumbHelper());
    }
}
