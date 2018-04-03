<?php

namespace Idk\LegoBundle;

use Idk\LegoBundle\DependencyInjection\Compiler\ComponentPass;
use Idk\LegoBundle\DependencyInjection\IdkLegoExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * IdkLegoBundle
 */
class IdkLegoBundle extends Bundle
{
    const VERSION = '1.17.9';

    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ComponentPass());
        //$container->registerExtension(new IdkLegoExtension());
    }
}
