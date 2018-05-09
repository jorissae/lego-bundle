<?php

namespace Idk\LegoBundle\EditInPlaceType;

use Idk\LegoBundle\Action\EditInPlaceAction;
use Symfony\Component\HttpFoundation\Request;

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

    public function getValueFromAction(Request $request, EditInPlaceAction $action)
    {
        return $action->getEntityManager()->getRepository($request->request->get('cls'))->find($request->request->get('value'));
    }
}
