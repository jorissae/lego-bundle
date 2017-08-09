<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;
use Idk\LegoBundle\FilterType\ORM as Type;
/**
 * @Annotation
 */
class StringFilter extends AbstractFilter
{


    public function getClassNameType(){
        return Type\StringFilterType::class;
    }
}
