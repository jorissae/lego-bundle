<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;
use Idk\LegoBundle\FilterType\ORM as Type;

/**
 * @Annotation
 */
class NotNullFilter extends AbstractFilter
{


    public function getClassNameType(){
        return Type\NotNullFilterType::class;
    }
}
