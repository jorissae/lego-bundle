<?php

namespace Idk\LegoBundle\Interfaces;

Interface Iworkflowable{
    public function getTransitionsPossible($object);
    public function getDefault();
    public function __toString();
}