<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Annotation\Entity;

/**
 * @Annotation
 */
class EntityForm extends Field
{
    private $fields;

    public function __construct(array $options = [])
    {
        $this->fields = (isset($options['fields']))? $options['fields']:[];
    }

    public function getFields(){
        return $this->fields;
    }
}