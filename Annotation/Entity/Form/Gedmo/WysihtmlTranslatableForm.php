<?php

namespace Idk\LegoBundle\Annotation\Entity\Form\Gedmo;

use Idk\LegoBundle\Form\Type\WysihtmlType as FieldType;
use Idk\LegoBundle\Form\Type\GedmoTranslatableType as BaseType;
use Idk\LegoBundle\Annotation\Entity\Form\AbstractForm;


/**
 * @Annotation
 */
class WysihtmlTranslatableForm extends AbstractForm
{

    public function __construct($options){
        parent::__construct($options);
        $this->options['fields_class'] = FieldType::class;
        $this->type = BaseType::class;
    }

}
