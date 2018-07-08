<?php

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Component\EntityReloadInterface;
use Idk\LegoBundle\Annotation\Entity\Field;

interface EditInPlaceInterface extends EntityReloadInterface {

    public function getFields();
    public function getField(string $fieldName):Field;
}