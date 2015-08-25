<?php

namespace Alchemy\PhraseanetBundle;

use Alchemy\PhraseanetBundle\DependencyInjection\PhraseanetExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PhraseanetBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new PhraseanetExtension();
    }
}
