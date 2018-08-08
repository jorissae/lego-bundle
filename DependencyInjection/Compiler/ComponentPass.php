<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;
use Idk\LegoBundle\Service\Tag\ComponentChain;

class ComponentPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(ComponentChain::class)) {
            return;
        }

        $definition = $container->findDefinition(ComponentChain::class);

        $taggedServices = $container->findTaggedServiceIds('lego.component');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addComponent', array(new Reference($id)));
        }
    }
}