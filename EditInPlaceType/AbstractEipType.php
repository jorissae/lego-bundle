<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
