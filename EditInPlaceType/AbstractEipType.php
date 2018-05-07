<?php

namespace Idk\LegoBundle\EditInPlaceType;

abstract class AbstractEipType{


    public function __construct(){

    }

    abstract public function getTemplate();

    public function getWithoutEipLayout(){
        return false;
    }

    public function formatValue($value){
        return nl2br((string)$value);
    }

    public function canToErase(){
        return false;
    }

    public function hasCallback(){
        return false;
    }
}
