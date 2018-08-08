<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
