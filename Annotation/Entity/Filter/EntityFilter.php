<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Annotation\Entity\Filter;

use Idk\LegoBundle\FilterType\ORM as Type;
/**
 * @Annotation
 */
class EntityFilter extends AbstractFilter
{

    protected $table;
    protected $method;
    protected $multiple;
    protected $args;

    public function init(){
        $this->table = $this->getOption('table');
        $this->method = $this->getOption('method');
        $this->multiple = $this->getOption('multiple');
        $this->args = $this->getOption('args');
    }


    public function getClassNameType(){
        return Type\EntityFilterType::class;
    }
}


