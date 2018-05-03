<?php
namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\Service\LegoInjectorInterface;
use Idk\LegoBundle\Service\Tag\InjectorChain;

class LegoInjector implements LegoInjectorInterface
{
    public function getTraits(){
        return [
            InjectorChain::CONTROLLER => '\Idk\LegoBundle\Traits\Controller',
        ];
    }

}
