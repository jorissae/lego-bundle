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
use Idk\LegoBundle\Lib\Pager;
use Symfony\Component\HttpFoundation\Request;
use Idk\LegoBundle\Lib\Actions\BulkAction;
use Doctrine\ORM\QueryBuilder;
use Idk\LegoBundle\Lib\QueryHelper;
use Idk\LegoBundle\Service\MetaEntityManager;

class TreeItems extends ListItems {

    protected function init(){

    }


    protected function requiredOptions(){
        return [];
    }

    public function isTree(){
        return true;
    }
    
    public function getPager(){
        $qb = $this->getConfigurator()->initQueryBuilderForComponent($this);
        return new Pager($qb, Pager::ALL);
    }

}
