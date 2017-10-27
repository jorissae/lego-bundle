<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Idk\LegoBundle\FilterType\ORM as Type;
use Idk\LegoBundle\Annotation\Entity\FieldForm;
use Idk\LegoBundle\Form\Type\WysihtmlType;


/**
 * @Annotation
 */
class WysihtmlForm extends FieldForm
{

    public function __construct($options){
        parent::__construct($options);
        $this->type = WysihtmlType::class;
    }

}
