<?php

namespace Idk\LegoBundle\Widget;

use Idk\LegoBundle\Service\GlobalsParametersProvider;

class UserWidget{

    private $em;
    private $security;
    private $parameters;

    public function __construct(GlobalsParametersProvider $parameters, EntityManagerInterface $em, TokenStorageInterface $security) {
        $this->em = $em;
        $this->security = $security;
        $this->parameters = $parameters;
    }

    public function getUsers(){
        return $this->em->getRepository($this->parameters->getUserClass())->findAll();
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Widget:widget_user.html.twig';
    }

    public function getParams(){
        return ['users'=>$this->getUsers()];
    }

}