<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;

use Idk\LegoBundle\FilterType\ORM as Type;

/**
 * @Annotation
 */
class DateFilter
{


    public function getClassNameType(){
        return Type\DateFilterType::class;
    }

}
