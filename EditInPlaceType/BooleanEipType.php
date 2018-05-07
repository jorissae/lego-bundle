<?php

namespace Idk\LegoBundle\EditInPlaceType;

class BooleanEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return 'IdkLegoBundle:EditInPlaceType:_entity.html.twig';
    }

    public function getWithoutEipLayout()
    {
        return true;
    }
}
