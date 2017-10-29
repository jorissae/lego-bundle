<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Idk\LegoBundle\Form\Type\JsonType;


/**
 * @Annotation
 */
class JsonForm extends AbstractForm
{

    public function __construct($options){
        parent::__construct($options);
        $this->type = JsonType::class;
    }

}
