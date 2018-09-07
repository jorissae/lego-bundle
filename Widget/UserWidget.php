<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
        return '@IdkLego/Widget/widget_user.html.twig';
    }

    public function getParams(){
        return ['users'=>$this->getUsers()];
    }

}