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

    public function getValueFromAction(Request $request, EditInPlaceAction $action)
    {
        $value = $request->request->get('value');
        if($value != ''){
            $value = \DateTime::createFromFormat('H:i',$request->request->get('value'));
        } else {
            $value = null;
        }
        return $value;
    }
}
