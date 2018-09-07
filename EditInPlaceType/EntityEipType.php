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

class EntityEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return '@IdkLego/EditInPlaceType/_entity.html.twig';
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
