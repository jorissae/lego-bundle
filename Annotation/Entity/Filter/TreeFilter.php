<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;
use Idk\LegoBundle\FilterType\ORM as Type;
/**
 * @Annotation
 */
class TreeFilter extends AbstractFilter
{
    protected $startLevel;

    public function init(){
        $this->startLevel = $this->getOption('start_level');
    }


    public function getClassNameType(){
        return Type\TreeFilterType::class;
    }

}
