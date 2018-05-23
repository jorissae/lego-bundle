<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType as BaseType;


/**
 * @Annotation
 */
class EntityForm extends AbstractForm
{

    public function __construct($options){
        parent::__construct($options);
        $this->type = BaseType::class;
    }

}
