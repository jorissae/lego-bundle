<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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
