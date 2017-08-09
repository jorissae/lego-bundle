<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;


use Idk\LegoBundle\FilterType\ORM as Type;
/**
 * @Annotation
 */
class ChoiceFilter extends AbstractFilter
{

    private $choices;
    private $multiple;

    public function init(){
        $this->choices = $this->getOption('choices');
        $this->multiple = $this->getOption('multiple');
    }

    public function getClassNameType(){
        return Type\ChoiceFilterType::class;
    }


}
