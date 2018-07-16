<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Annotation\Entity\Field;
use Idk\LegoBundle\Lib\Actions\EntityAction;
use Idk\LegoBundle\Lib\Breaker;
use Symfony\Component\HttpFoundation\Request;
use Idk\LegoBundle\Lib\Actions\BulkAction;
use Doctrine\ORM\QueryBuilder;
use Idk\LegoBundle\Lib\QueryHelper;
use Idk\LegoBundle\Service\MetaEntityManager;

class TreeItems extends Component{


    protected function init(){

    }


    protected function requiredOptions(){
        return [];
    }

    public function getTemplate($name = 'index'){
        return 'IdkLegoBundle:Component\\TreeItemsComponent:'.$name.'.html.twig';
    }

    public function getTemplateParameters(){
        return ['component' => $this];
    }

    public function getEntities(){
        return $this->getConfigurator()->getQueryBuilder()->orderBy('b.left')->getQuery()->getResult();
    }

}
