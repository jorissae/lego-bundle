<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service;


use Idk\LegoBundle\EditInPlaceType\BooleanEipType;
use Idk\LegoBundle\EditInPlaceType\DateEipType;
use Idk\LegoBundle\EditInPlaceType\DateTimeEipType;
use Idk\LegoBundle\EditInPlaceType\EntityEipType;
use Idk\LegoBundle\EditInPlaceType\StringEipType;
use Idk\LegoBundle\EditInPlaceType\TimeEipType;
use Doctrine\ORM\PersistentCollection;
use Symfony\Component\Config\Definition\Exception\Exception;

class EditInPlaceFactory
{

    public function __construct() {
    }

    public function getEditInPlaceType(?string $type, $value, $name){
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
            throw new Exception('Can\'t use edit in place for Collection values ('.$name.')');
            $class=  new StringEipType();
        } elseif(is_array($value)) {
            //TODO return 'array';
            throw new Exception('Can\'t use edit in place for Array values ('.$name.')');
            $class=  new StringEipType();
        } elseif($type != null) {
            $class=  new StringEipType();
        } else {
            $class = new EntityEipType();
        }
        return $class;
    }


}
