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

    public function getTemplate(){
        return 'IdkLegoBundle:Widget:widget.html.twig';
    }

    public function getWidgetsTemplate(){
        return 'IdkLegoBundle:Widget:widgets.html.twig';
    }



    public function getUseWidgets(){
        $this->getWidgets();
    }

    public function getNoUseWidgets(){
        return $this->getWidgets();
    }

    public function getCurrentWidgetId(){
        return $this->getWidgets();
    }

    public function saveWidget($request){
        $order = $request->request->get('order');
        if($order == null) $order = [];
    }

}