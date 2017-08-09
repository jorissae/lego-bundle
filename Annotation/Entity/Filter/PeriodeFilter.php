<?php

namespace Idk\LegoBundle\Annotation\Entity\Filter;
use Idk\LegoBundle\FilterType\ORM as Type;

/**
 * @Annotation
 */
class PeriodeFilter extends AbstractFilter
{

    private $choices;
    private $requestChoice;

    public function init(){
        $this->choices = $this->getOption('choices');
        $this->requestChoice = $this->getOption('requestChoice');
    }


    public function getClassNameType(){
        return Type\PeriodeFilterType::class;
    }

}
