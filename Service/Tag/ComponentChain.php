<?php
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

    public function build($classname, $options, $configurateur, $routeSuffix){
        $component = clone $this->components[$classname];
        return $component->build($options,$configurateur,$routeSuffix);
    }
}