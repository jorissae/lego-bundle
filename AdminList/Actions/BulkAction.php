<?php

namespace Idk\LegoBundle\AdminList\Actions;

/**
 * The simple bulk action is a default implementation of the bulk action interface, this can be used
 * in very simple use cases.
 */
class BulkAction
{
    /**
     * @var array
     */
    private $url;

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

    private $type;

    private $value;

    private $choices;

    private $field;

    private $role;

    private $id;

    private $cid;

    private $params;

    /**
     * @param array    $url      The url path and parameters
     * @param string   $label    The label
     * @param string   $icon     The icon
     * @param string   $template The template
     */
    public function __construct($label, $options)
    {
        $this->label = $label;
        $this->id = md5($label);
        $this->route = (isset($options['route']))? $options['route']:null;
        $this->icon = (isset($options['icon']))? $options['icon']:null;
        $this->type = (isset($options['type']))? $options['type']:null;
        $this->field = (isset($options['field']))? $options['field']:null;
        $this->value = (isset($options['value']))? $options['value']:null;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->choices = (isset($options['choices']))? $options['choices']:null;
        $this->role = (isset($options['role']))? $options['role']:null;
        $this->params = (isset($options['params']))? $options['params']:[];
    }

    public function getId(){
        return $this->id;
    }

    public function setCid($cid){
        $this->cid = $cid;
    }

    /**
     * @return array
     */
    public function getUrl()
    {
        return array('path'=>$this->route,'params'=> array_merge($this->params, ['ida'=>$this->id, 'cid' => $this->cid]));
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    public function getField(){
        return $this->field;
    }

    public function getValue(){
        return $this->value;
    }

    public function getChoices(){
        return $this->choices;
    }

    public function getRole(){
        return $this->role;
    }
}
