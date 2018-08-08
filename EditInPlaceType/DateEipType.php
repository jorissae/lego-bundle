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
