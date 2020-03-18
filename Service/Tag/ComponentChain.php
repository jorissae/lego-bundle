<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Service\Tag;

use Idk\LegoBundle\Component\Component;
use Symfony\Component\DependencyInjection\Reference;

class ComponentChain
{
    private $components;

    public function __construct()
    {
        $this->components = [];
    }

    public function addComponent(Component $component)
    {
        $this->components[get_class($component)] = $component;
    }

    public function getComponents(){
        return $this->components;
    }

    public function build($classname, $options, $configurateur, $routeSuffix){
        $component = clone $this->components[$classname];
        return $component->build($options,$configurateur,$routeSuffix);
    }
}
