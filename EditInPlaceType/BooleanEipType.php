<?php

namespace Idk\LegoBundle\EditInPlaceType;

use Idk\LegoBundle\Action\EditInPlaceAction;
use Symfony\Component\HttpFoundation\Request;

class BooleanEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return 'IdkLegoBundle:EditInPlaceType:_boolean.html.twig';
    }

    public function getWithoutEipLayout()
    {
        return true;
    }

    public function getValueFromAction(Request $request, EditInPlaceAction $action)
    {
        return ($request->request->get('value') === '1');
    }
}
