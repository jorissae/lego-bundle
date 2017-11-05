<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextareaType as ParentType;

class CkEditorType extends AbstractType
{
    public function getParent()
    {
        return ParentType::class;
    }

    public function getName()
    {
        return 'lego_ckeditor';
    }


}



?>
