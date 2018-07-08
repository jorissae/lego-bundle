<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Idk\LegoBundle\Form\Type\ManyToManyJoinType as BaseType;

/**
 * @Annotation
 */
class ManyToManyJoinForm extends AbstractForm
{

    public function __construct($options){
        $options['by_reference'] = false;
        parent::__construct($options);
        $this->type = BaseType::class;
    }


}
