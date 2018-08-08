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

use Symfony\Component\Form\Extension\Core\Type\FileType as BaseType;
use Symfony\Component\Form\FormFactoryInterface;


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
