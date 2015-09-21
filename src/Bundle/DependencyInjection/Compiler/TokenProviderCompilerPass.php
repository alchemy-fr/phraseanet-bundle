<?php

namespace Alchemy\PhraseanetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class TokenProviderCompilerPass implements CompilerPassInterface
{

    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $service = $container->findDefinition('phraseanet.token_provider');
        $providerIds = $container->findTaggedServiceIds('phraseanet.token_provider');

        foreach ($providerIds as $id => $tags) {
            $service->addMethodCall('addProvider', array(new Reference($id)));
        }
    }
}
