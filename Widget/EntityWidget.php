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
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class EntityWidget extends Widget {

    private $mem;

    public function __construct(MetaEntityManager $mem) {
        $this->mem = $mem;
    }

    public function getEntities(){
        return $this->mem->getMetaDataEntities();
    }

    public function getTemplate(){
        return '@IdkLego/Widget/widget_entity.html.twig';
    }

    public function getParams(){
        return ['entities'=>$this->getEntities()];
    }

}