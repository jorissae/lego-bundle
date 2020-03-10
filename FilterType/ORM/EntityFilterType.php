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
use Symfony\Component\HttpFoundation\Request;

class EntityFilterType extends AbstractORMFilterType
{

    protected $table;
    protected $method;
    protected $multiple;
    protected $args;
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request  The request
     * @param array   &$data    The data
     * @param string  $uniqueId The unique identifier
     */
    public function bindRequest(array &$data, $uniqueId)
    {
        $data['comparator'] = $this->getValueSession('filter_comparator_' . $uniqueId);
        $data['value']      = $this->getValueSession('filter_value_' . $uniqueId);
        return ($data['value'] != '');
    }

     /**
     * @param string $columnName The column name
     * @param string $alias      The alias
     */
    public function load($columnName, $config = array(), $alias = 'b')
    {

        parent::load($columnName, $config, $alias);
        $this->table = $config['table'];
        $this->method = (isset($config['method']))? $config['method']:'findAll';
        $this->args = (isset($config['arguments']))? $config['arguments']:null;
        $this->multiple = (isset($config['multiple']))? $config['multiple']:true;
    }


    /**
     * @param array  $data     The data
     * @param string $uniqueId The unique identifier
     */
    public function apply(array $data, $uniqueId,$alias,$col)
    {
        if (isset($data['value'])) {
            $qb = $this->queryBuilder;
            if($this->getMultiple()){
                $qb->andWhere($qb->expr()->in($alias . $col, ':var_' . $uniqueId));
            }else{
                $qb->andWhere($qb->expr()->eq($alias . $col, ':var_' . $uniqueId));
            }
            $qb->setParameter('var_' . $uniqueId, $data['value']);
        }
    }



    public function getEntities($data){
        if($this->isHidden()){ //si le filtre est hidden pas de requet. héééé ouai
            $elements = array();
            if(is_array($data['value'])){
                foreach($data['value'] as $value) $elements[] = new HiddenEntity($value);
            }else{
                $elements[] = new HiddenEntity($data['value']);
            }
        }else{
            $m = $this->method;
            $args = $this->args;
            $repo = $this->em->getRepository($this->table);
            if($args){
                $classRfx = new \ReflectionClass(get_class($repo));
                $methodRfx = $classRfx->getMethod($m);
                $elements = $methodRfx->invokeArgs($repo,$args);
            }else{
                $elements = $repo->$m();
            }
        }
        return $elements;
    }

    public function isSelected($data,$entity){
        if($this->getMultiple() and is_array($data['value'])){
            return in_array($entity->getId(),$data['value']);
        }else{
            return ($data && $data['value'] == $entity->getId());
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return '@IdkLego/FilterType/entityFilter.html.twig';
    }

    public function getMultiple(){
        return $this->multiple;
    }
}


/**
 * StringFilterType
 */

class HiddenEntity{
    private $value;
    public function __construct($value){
        $this->value = $value;
    }

    public function __toString(){
        return (string)$this->value;
    }

    public function getId(){
        return $this->value;
    }
}
