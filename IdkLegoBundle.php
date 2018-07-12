<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle;

use Idk\LegoBundle\DependencyInjection\Compiler\ComponentPass;
use Idk\LegoBundle\DependencyInjection\Compiler\WidgetPass;
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
        //$container->addCompilerPass(new WidgetPass());
        //$container->registerExtension(new IdkLegoExtension());
    }
}
