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

class StringEipType extends AbstractEipType{


    public function __construct(){

    }

    public function getTemplate(){
        return '@IdkLego/EditInPlaceType/_string.html.twig';
    }
}
