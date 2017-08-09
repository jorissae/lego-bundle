<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;

use Idk\LegoBundle\FilterType\ORM as Type;
/**
 * @Annotation
 */
class EntityFilter extends AbstractFilter
{

    protected $table;
    protected $method;
    protected $multiple;
    protected $args;

    public function init(){
        $this->table = $this->getOption('table');
        $this->method = $this->getOption('method');
        $this->multiple = $this->getOption('multiple');
        $this->args = $this->getOption('args');
    }


    public function getClassNameType(){
        return Type\EntityFilterType::class;
    }
}


