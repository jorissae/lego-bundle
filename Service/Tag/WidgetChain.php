<?php
namespace Idk\LegoBundle\Service\Tag;


class WidgetChain
{
    private $widgets = [];

    public function __construct(iterable $widgets)
    {
        foreach($widgets as $widget){
            $this->widgets[] = $widget;
        }
    }

    public function addWidget($widget)
    {
        $this->widgets[] = $widget;
    }


    public function getWidgets():iterable{
        return $this->widgets;
    }
}