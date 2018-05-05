<?php

namespace Idk\LegoBundle\Widget;

use Idk\LegoBundle\Widget\Widget;
use Idk\LegoBundle\Service\GlobalsParametersProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserWidget extends Widget {

    private $em;
    private $security;
    private $userClass;

    public function __construct($userClass, EntityManagerInterface $em, TokenStorageInterface $security) {
        $this->em = $em;
        $this->security = $security;
        $this->userClass = $userClass;
    }

    public function getUsers(){
        return $this->em->getRepository($this->userClass)->findAll();
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Widget:widget_user.html.twig';
    }

    public function getParams(){
        return ['users'=>$this->getUsers()];
    }

}