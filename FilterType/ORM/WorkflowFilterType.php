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

use App\Entity\Examen;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Workflow\Registry;

/**
 * StringFilterType
 */
class WorkflowFilterType extends ChoiceFilterType
{

    private $choices;
    private $excludes;
    private $multiple;
    private $registry;
    private $em;


    public function __construct(EntityManagerInterface $em, Registry $registry)
    {
        $this->em = $em;
        $this->registry = $registry;
    }

    public function load($columnName, $config = array(), $alias = 'b')
    {
        $config['choices'] = $config['choices'] ?? $this->registry->get(
                $this->em->getClassMetadata($config['class'] ?? $config['data_class'])->newInstance(),
                $config['name'] ?? null)->getDefinition()->getPlaces();
        $this->multiple = $config['multiple'] ?? true;
        parent::load($columnName, $config, $alias);
        $this->excludes = $config['excludes'] ?? [];

    }


    public function apply(array $data, $uniqueId, $alias, $col)
    {
        $qb = $this->queryBuilder;
        if (isset($data['value'])) {
            if($this->getMultiple()){
                $qb->andWhere($qb->expr()->in($alias.$col, ':var_' . $uniqueId));
            } else {
                $qb->andWhere($qb->expr()->eq($alias.$col, ':var_' . $uniqueId));
            }
            $qb->setParameter('var_' . $uniqueId, $data['value']);
        } elseif (!empty($this->excludes)) {
            $qb->andWhere($qb->expr()->notin($alias.$col, ':var_' . $uniqueId));
            $qb->setParameter('var_' . $uniqueId, $this->excludes);
            
        }
    }


    public function isSelected($data,$value){
        if(is_null($data['value'])){
            return !in_array($value, $this->excludes);
        }
        if(is_array($data['value'])){
            return in_array($value,$data['value']);
        }else{
            return ($data['value'] == $value);
        }
    }

    public function getTemplate()
    {
        return '@IdkLego/FilterType/workflowFilter.html.twig';
    }

}
