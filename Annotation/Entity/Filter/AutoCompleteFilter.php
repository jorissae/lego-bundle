<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;

use Idk\LegoBundle\FilterType\ORM as Type;


/**
 * @Annotation
 */
class AutoCompleteFilter extends AbstractFilter
{

    protected $route;

    public function init(){
        $this->route = $this->getOption('route');
    }

    public function getClassNameType(){
        return Type\AutoCompleteFilterType::class;
    }

}
