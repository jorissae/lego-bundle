<?php

namespace Idk\LegoBundle\EditInPlaceType;

class DateTimeEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return 'IdkLegoBundle:EditInPlaceType:_datetime.html.twig';
    }

    public function formatValue($value){
        return $value->format('d/m/Y H:i');
    }
}
