<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Component\EntityReloadInterface;
use Idk\LegoBundle\Annotation\Entity\Field;

interface EditInPlaceInterface extends EntityReloadInterface {

    public function getFields();
    public function getField(string $fieldName):Field;
}