<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Idk\LegoBundle\Form\Type\NoteType as BaseType;


/**
 * @Annotation
 */
class NoteForm extends AbstractForm
{

    public function __construct($options){
        parent::__construct($options);
        $this->type = BaseType::class;
    }

}
