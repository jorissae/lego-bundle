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
class TreeFilter extends AbstractFilter
{
    protected $startLevel;

    public function init(){
        $this->startLevel = $this->getOption('start_level');
    }


    public function getClassNameType(){
        return Type\TreeFilterType::class;
    }

}
