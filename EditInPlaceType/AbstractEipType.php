<?php

namespace Idk\LegoBundle\EditInPlaceType;

use Idk\LegoBundle\Action\EditInPlaceAction;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractEipType{


    public function __construct(){

    }

    abstract public function getTemplate();

    public function getValueFromAction(Request $request, EditInPlaceAction $action){
        return $request->request->get('value');
    }

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
