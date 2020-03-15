<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Annotation\Entity\Form;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType as BaseType;


/**
 * @Annotation
 */
class ChoiceForm extends AbstractForm
{

    public function __construct($options){
        $options['attr'] = array_merge($options['attr'] ?? [], ['class' => 'form-control']);
        parent::__construct($options);
        $this->type = BaseType::class;
    }

}
