<?php

namespace Idk\LegoBundle\Widget;

use Idk\LegoBundle\Service\GlobalsParametersProvider;
use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EntityWidget{

    private $mem;

    public function __construct(MetaEntityManager $mem) {
        $this->mem = $mem;
    }

    public function getEntities(){
        return $this->mem->getMetaDataEntities();
    }

    public function getTemplate(){
        return 'IdkLegoBundle:Widget:widget_entity.html.twig';
    }

    public function getParams(){
        return ['entities'=>$this->getEntities()];
    }

}