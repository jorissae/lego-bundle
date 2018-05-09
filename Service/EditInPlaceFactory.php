<?php
namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\EditInPlaceType\BooleanEipType;
use Idk\LegoBundle\EditInPlaceType\DateEipType;
use Idk\LegoBundle\EditInPlaceType\DateTimeEipType;
use Idk\LegoBundle\EditInPlaceType\EntityEipType;
use Idk\LegoBundle\EditInPlaceType\StringEipType;
use Idk\LegoBundle\EditInPlaceType\TimeEipType;

class EditInPlaceFactory
{

    public function __construct() {
    }

    public function getEditInPlaceType(?string $type, $value){
        if ($type == 'boolean') {
            $class = new BooleanEipType();
        }else if($type == 'datetime'){
            $class = new DateTimeEipType();
        }else if($type == 'date') {
            $class = new DateEipType();
        }else if($type == 'time') {
            $class =  new TimeEipType();
        } else if($value instanceof PersistentCollection) {
            //TODO return 'collection';
            $class=  new StringEipType();
        } elseif(is_array($value)) {
            //TODO return 'array';
            $class=  new StringEipType();
        } elseif($type != null) {
            $class=  new StringEipType();
        } else {
            $class = new EntityEipType();
        }
        return $class;
    }


}
