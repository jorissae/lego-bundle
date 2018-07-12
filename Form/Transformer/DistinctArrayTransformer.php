<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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