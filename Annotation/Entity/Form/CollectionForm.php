<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Symfony\Component\Form\Extension\Core\Type\CollectionType as BaseType;

/**
 * @Annotation
 */
class CollectionForm extends AbstractForm
{

    public function __construct($options){
        parent::__construct($options);
        $this->type = BaseType::class;
    }
}
