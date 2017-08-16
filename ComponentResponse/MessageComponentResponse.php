<?php

namespace Idk\LegoBundle\ComponentResponse;


class MessageComponentResponse extends ComponentResponse{

    private $type = self::NOTICE;
    private $message = null;
    private $redirectPath = null;
    private $redirectParams = [];

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

    public function setRedirect($path, $params = []){
        $this->redirectPath = $path;
        $this->redirectParams = $params;
        return $this;
    }

    public function getRedirect(){
        if($this->hasRedirect()) {
            return ['path' => $this->redirectPath, 'params' => $this->redirectParams];
        }
        return null;
    }

    function hasRedirect(){
        return ($this->redirectPath !== null);
    }


}
