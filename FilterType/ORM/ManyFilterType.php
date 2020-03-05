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


/**
 * StringFilterType
 */
class ManyFilterType extends EntityFilterType
{

    protected $join;
    protected $multiple;

     /**
     * @param string $columnName The column name
     * @param string $alias      The alias
     */
    public function load($columnName, $config = array(), $alias = 'b')
    {
        parent::load($columnName, $config, $alias);
        $this->join = $config['join'];
        $this->multiple = (isset($config['multiple']))? $config['multiple']:false;
    }


    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId,$alias,$col)
    {   
        if (isset($data['value'])) {
            $join =  $alias.$this->join;
            $this->queryBuilder->innerJoin($join,'j');
            $this->queryBuilder->andWhere('j.' . $col.' = :var_' . $uniqueId)->setParameter('var_' . $uniqueId, $data['value']);
        }
    }

    public function getEntities($data){
        $em = $this->em; 
        $m = $this->method;
        $elements = $em->getRepository($this->table)->$m();
        return $elements;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@IdkLego:FilterType/manyFilter.html.twig';
    }

    public function getMultiple(){
        return $this->multiple;
    }
}
