<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;
use Idk\LegoBundle\FilterType\ORM as Type;
/**
 * @Annotation
 */
class ManyFilter extends AbstractFilter
{

    protected $join;
    protected $multiple;

    public function init(){
        $this->join = $this->getOption('join');
        $this->multiple = $this->getOption('multiple');
    }


    public function getClassNameType(){
        return Type\ManyFilterType::class;
    }
}
