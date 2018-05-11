<?php

namespace Idk\LegoBundle\Component;


use Idk\LegoBundle\Annotation\Entity\Field;
use Idk\LegoBundle\Form\Type\AutoCompletionType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

interface EntityReloadInterface{
    public function renderEntity($entity);
}