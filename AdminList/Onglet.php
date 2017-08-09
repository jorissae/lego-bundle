<?php

namespace Idk\LegoBundle\AdminList;

/**
 * Field
 */
class Onglet
{


    /**
     * @var string
     */
    private $name;

    private $route;

    private $params;

    private $template;

    private $sublist;

    private $widget;

    private $controller;
    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct($name, array $options)
    {
        $this->name     = $name;
        $this->route = (isset($options['route']))? $options['route']:null;
        $this->controller = (isset($options['controller']))? $options['controller']:null;
        $this->params = (isset($options['params']))? $options['params']:null;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->widget = (isset($options['widget']))? $options['widget']:null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getRoute(){
        return $this->route;
    }

    public function getController(){
        return $this->controller;
    }

    public function getTemplate(){
        return $this->template;
    }

    public function getWidget(){
        return $this->widget;
    }

    public function getParams($item){
        $return = array();
        foreach($this->params as $k => $call){
            $return[$k] = $item->$call();
        }
        return $return;
    }


}
