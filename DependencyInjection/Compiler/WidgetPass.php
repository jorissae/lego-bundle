<?php
namespace Idk\LegoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Idk\LegoBundle\Service\Tag\WidgetChain;

class WidgetPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(WidgetChain::class)) {
            return;
        }

        $definition = $container->findDefinition(WidgetChain::class);

        $taggedServices = $container->findTaggedServiceIds('lego.widget');

        foreach ($taggedServices as $id => $tags) {
            die($id);
            $definition->addMethodCall('addWidget', array(new Reference($id)));
        }
    }
}