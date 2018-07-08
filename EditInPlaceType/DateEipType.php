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

    public function getValueFromAction(Request $request, EditInPlaceAction $action)
    {
        $value = $request->request->get('value');
        if($value != ''){
            $value = \DateTime::createFromFormat('d/m/Y',$request->request->get('value'));
        } else {
            $value = null;
        }
        return $value;
    }
}
