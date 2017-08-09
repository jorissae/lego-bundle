<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;

use Idk\LegoBundle\FilterType\ORM as Type;

/**
 * @Annotation
 */
class DateTimeFilter extends AbstractFilter
{


    public function getClassNameType(){
        return Type\DateTimeFilterType::class;
    }
}
