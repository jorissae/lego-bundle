<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Idk\LegoBundle\Form\Type\JsonHumanType as BaseType;


/**
 * @Annotation
 */
class JsonHumanForm extends AbstractForm
{

    public function __construct($options){
        parent::__construct($options);
        $this->type = BaseType::class;
    }

}
