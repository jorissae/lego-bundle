<?php

namespace Idk\LegoBundle\EditInPlaceType;

class DateEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return 'IdkLegoBundle:EditInPlaceType:_date.html.twig';
    }

    public function formatValue($value){
        return $value->format('d/m/Y');
    }
}
