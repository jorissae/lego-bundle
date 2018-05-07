<?php

namespace Idk\LegoBundle\EditInPlaceType;

class TimeEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return 'IdkLegoBundle:EditInPlaceType:_time.html.twig';
    }

    public function formatValue($value){
        return $value->format('H:i');
    }
}
