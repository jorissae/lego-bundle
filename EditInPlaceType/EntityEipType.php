<?php

namespace Idk\LegoBundle\EditInPlaceType;

class EntityEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return 'IdkLegoBundle:EditInPlaceType:_entity.html.twig';
    }

    public function canToErase()
    {
        return true;
    }

    public function hasCallback()
    {
        return true;
    }
}
