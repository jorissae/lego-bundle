<?php

namespace Idk\LegoBundle\ComponentResponse;


class SuccessComponentResponse extends MessageComponentResponse{

    public function __construct($message){
        parent::__construct($message,MessageComponentResponse::SUCCESS);
    }

    public function setType($type)
    {
        throw new \Exception('You can\'t change the type of '.get_class($this).' use MessageComponentResponse');
    }

}
