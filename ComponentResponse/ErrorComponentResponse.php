<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\ComponentResponse;


class ErrorComponentResponse extends MessageComponentResponse{

    public function __construct($message){
        parent::__construct($message,MessageComponentResponse::ERROR);
    }

    public function setType($type)
    {
        throw new \Exception('You can\'t change the type of '.get_class($this).' use MessageComponentResponse');
    }
}
