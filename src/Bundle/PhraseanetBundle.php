<?php

namespace Alchemy\PhraseanetBundle;

use Alchemy\PhraseanetBundle\DependencyInjection\Compiler\TokenProviderCompilerPass;
use Alchemy\PhraseanetBundle\DependencyInjection\PhraseanetExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PhraseanetBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new TokenProviderCompilerPass());
    }

    public function getContainerExtension()
    {
        return new PhraseanetExtension();
    }
}
