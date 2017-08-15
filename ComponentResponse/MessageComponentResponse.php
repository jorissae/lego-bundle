<?php

namespace Idk\LegoBundle\ComponentResponse;


class MessageComponentResponse extends ComponentResponse{

    private $type = self::NOTICE;
    private $message = null;

    const NOTICE = 'notice';
    const SUCCESS = 'notice';
    const WARNING = 'warning';
    const ERROR = 'error';
    const INFO = 'info';

    public function __construct($message = null, $type = self::NOTICE){
        $this->message = $message;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param null $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


}
