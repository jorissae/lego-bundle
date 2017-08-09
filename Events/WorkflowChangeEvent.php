<?php
namespace Idk\LegoBundle\Events;

use Symfony\Component\EventDispatcher\Event;
use Idk\LegoBundle\Interfaces\Iworkflowable;

class WorkflowChangeEvent extends Event
{

    protected $item;
    protected $fromWork;
    protected $toWork;
    protected $user;


    public function __construct($item, $user, Iworkflowable $fromWork, Iworkflowable $toWork){
        $this->item = $item;
        $this->fromWork = $fromWork;
        $this->toWork = $toWork;
        $this->user = $user;
    }

    public function getFromWork(){
        return $this->fromWork;
    }

    public function getToWork(){
        return $this->toWork;
    }

    public function getUser(){
        return $this->user;
    }

    public function getItem(){
        return $this->item;
    }
}