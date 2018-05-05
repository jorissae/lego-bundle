<?php

namespace Idk\LegoBundle\Widget;

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Idk\LegoBundle\Widget\Widget;

class ListWidget extends Widget{


    private $provider;

    public function __construct($provider){
        $this->provider = $provider;
    }

    public function getName(){
        return 'widget.list_widget';
    }

    public function getDescription(){
        return 'widget.list_widget_description';
    }

    public function getId(){
        return 'list_widget';
    }

    public function getTemplate(){
        return "IdkLegoBundle:Widget:list_widget.html.twig";
    }

    public function getClassCss(){
        return 'col-md-12';
    }

    public function getActive(){
        return false;
    }

    public function getParams(){
        return ['widgets' => $provider->getWidgets()];
    }
}
