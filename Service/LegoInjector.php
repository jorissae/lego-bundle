<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\Service\LegoInjectorInterface;
use Idk\LegoBundle\Service\Tag\InjectorChain;

class LegoInjector implements LegoInjectorInterface
{
    public function getTraits(){
        return [
            InjectorChain::CONTROLLER => '\Idk\LegoBundle\Traits\ControllerTrait',
        ];
    }

}
