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
