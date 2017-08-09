<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;

use Idk\LegoBundle\FilterType\ORM as Type;
/**
 * @Annotation
 */
class BooleanFilter extends AbstractFilter
{

    public function getClassNameType(){
        return Type\BooleanFilterType::class;
    }
}
