<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\FilterType\ORM;

use Doctrine\ORM\EntityManagerInterface;
use Idk\LegoBundle\FilterType\AbstractFilterType;

use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityManager;
use Idk\LegoBundle\Lib\QueryHelper;

/**
 * The abstract filter used for ORM query builder
 */
abstract class AbstractORMFilterType extends AbstractFilterType
{
    /**
     * @var QueryBuilder $queryBuilder
     */
    protected $queryBuilder;

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function setQueryBuilder(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getColumnName(){
        return $this->columnName;
    }

    public function init(){
        $queryHelper = new QueryHelper();
        $path = $queryHelper->getPath($this->queryBuilder,str_replace('.','',$this->getAlias()),$this->columnName);
        //echo $this->getAlias().':'.$this->columnName.'  --> '.$path['alias'].$path['column'].'<br/>';
        return $path;
    }




}
