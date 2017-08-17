<?php

namespace Idk\LegoBundle\AdminList\Actions;

/**
 * The simple item action is a default implementation of the item action interface, this can be used
 * in very simple use cases.
 */
class EntityAction
{

    /**
     * @var callable
     */
    private $routerGenerator;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var string
     */
    private $label;

    /**
     * @var null|string
     */
    private $template;

    /**
     * @var null|array
     */
    private $option;

    private $field;

    private $type;

    private $xhr;

    private $popup;

    private $custom;

    private $route;

    private $if;

    private $id;

    private $target;

    private $params;

    private $modal;

    private $cssClass;

    /**
     * @param callable $routerGenerator The generator used to generate the url of an item, when generating the item will
     *                                  be provided.
     * @param string   $icon            The icon
     * @param string   $label           The label
     * @param string   $template        The template
     */
    public function __construct($label,$options)
    {

        $this->label = $label;
        $this->routerGenerator = (isset($options['route_callback']))? $options['route_callback']:null;
        $this->route = (isset($options['route']))? $options['route']:null;
        $this->icon = (isset($options['icon']))? $options['icon']:null;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->type = (isset($options['type']))? $options['type']:null;
        $this->field = (isset($options['field']))? $options['field']:null;
        $this->if = (isset($options['if']))? $options['if']:null;
        $this->target = (isset($options['target']))? $options['target']:null;
        $this->params = (isset($options['params']))? $options['params']:null;
        $this->popup = (isset($options['popup']))? $options['popup']:null;
        $this->modal = (isset($options['modal']))? $options['modal']:null;
        $this->cssClass = (isset($options['css_class']))? $options['css_class']:null;

        $this->id = md5($label);
        $this->raise();
        $this->initCustom($options);
        $this->initXhr($options);

    }

    public function isPopup(){
        return $this->popup;
    }

    public function isModal(){
        return $this->modal;
    }

    public function getModal(){
        return $this->modal;
    }

    public function getCssClass(){
        return $this->cssClass;
    }

    public function isShow($item){
        if(is_array($this->if)){
            $m = $this->if['method'];
            $v = $this->if['value'];
            $args = (isset($this->if['args']))? $this->if['args']:null;
            if($args){
                if(is_array($this->if['args'])){
                    $returnCall = call_user_func_array(array($item, $m), $this->if['args']);
                } else {
                    $returnCall =  $item->$m($this->if['args']);
                }
            }else{
                $returnCall = $item->$m();
            }
            return ($returnCall == $v);
        }else{
            return true;
        }
    }

    public function getId(){
        return $this->id;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getUrlFor($item)
    {
        $routeGenerator = $this->routerGenerator;
        if ($routeGenerator && is_callable($routeGenerator)) {
           return $routeGenerator($item);
        } elseif($this->params){
            return array('path'=>$this->route,'params'=>$this->getParams());
        } elseif($this->route){
            return array('path'=>$this->route,'params'=>array('id'=>$item->getId()));
        }

        return null;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getIconFor($item)
    {
        return $this->icon;
    }

    /**
     * @param mixed $item
     *
     * @return string
     */
    public function getLabelFor($item)
    {
        if($this->custom){
            $loader = new \Twig_Loader_String();
            $twig = new \Twig_Environment($loader);
            $render = $twig->render($this->custom,array('item'=>$item,'label'=>$this->label));
            return $render;
        }else{
            return $this->label;
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return array
     */
    public function getOption()
    {
        return $this->option;
    }

    public function getType(){
        return $this->type;
    }

    public function getField(){
        return $this->field;
    }

    public function isXhr(){
        return $this->xhr;
    }

    public function getTarget(){
        return $this->target;
    }

    public function getParams(){
        return $this->params;
    }

    private function initXhr($options){
        $this->xhr = false;
        if($this->type == 'toggle'){
            $this->xhr= true;
        }
        $this->xhr = (isset($options['xhr']))? $options['xhr']:$this->xhr;
    }

    private function initCustom($options){
        $this->custom = false;
        if($this->type == 'toggle'){
            $this->custom = '{% if item.'.$this->field.' %} Dé{{ label|lower }} {% else %} {{ label|capitalize }} {% endif %}';
        }
        $this->custom = (isset($options['custom']))? $options['custom']:$this->custom;
    }

    private function raise(){
        if($this->type == 'toggle' and $this->field == null){
            throw new \Exception('Le type toggle fonctionne avec l\'option "field"');
        }
    }

}
