<?php
namespace Idk\LegoBundle\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;


class DistinctArrayTransformer implements DataTransformerInterface
{

    public function transform($array)
    {
        if (null === $array) {
            return [];
        }
        return array_unique($array);
    }

    public function reverseTransform($array)
    {
        return  array_unique($array);
    }
}