<?php

namespace Idk\LegoBundle\EditInPlaceType;

class StringEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return 'IdkLegoBundle:EditInPlaceType:_string.html.twig';
    }
}
