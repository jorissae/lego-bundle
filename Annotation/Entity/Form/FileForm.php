<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType as BaseType;


/**
 * @Annotation
 */
class FileForm extends AbstractForm
{

    public function __construct($options){
        parent::__construct($options);
        $this->type = BaseType::class;
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options['data_class'] = $options['data_class'] ?? null;
        return $options;
    }

}
