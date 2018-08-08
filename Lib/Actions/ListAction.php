<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Lib\Actions;

use Idk\LegoBundle\Lib\Path;
/**
 * The simple list action is a default implementation of the list action interface, this can be used
 * in very simple use cases.
 */
class ListAction
{
    /**
     * @var array
     */
    private $route;

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

    private $value;

    private $field;

    private $id;

    private $sublist;

    private $params;

    private $position;

    private $target;

    private $role;

    /**
     * @param array    $url      The url path and parameters
     * @param string   $label    The label
     * @param string   $icon     The icon
     * @param string   $template The template
     */
    public function __construct($label,$options)
    {
        $this->label = $label;
        $this->id = md5($label);
        $this->route = (isset($options['route']))? $options['route']:null;
        $this->icon = (isset($options['icon']))? $options['icon']:null;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->type = (isset($options['type']))? $options['type']:null;
        $this->value = (isset($options['value']))? $options['value']:null;
        $this->field = (isset($options['field']))? $options['field']:null;
        $this->sublist = (isset($options['sublist']))? $options['sublist']:false;
        $this->params = (isset($options['params']))? $options['params']:array();
        $this->position = (isset($options['position']))? $options['position']:'top';
        $this->target = (isset($options['target']))? $options['target']:null;
        $this->role = (isset($options['role']))? $options['role']:null;
        $this->url = (isset($options['url']))? $options['url']:null;
    }

    public function getId(){
        return $this->id;
    }

    public function getTarget(){
        return $this->target;
    }

    public function getRole(){
        return $this->role;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getPath()
    {
        return new Path($this->route, $this->params);
    }

    /**
     * @return string
     */
    public function getIcon()
    {
        return $this->icon;
    }

    public function getParams(){
        return $this->params;
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

    public function isSublist(){
        return $this->sublist;
    }

    public function isTop(){
        return ($this->position != 'bottom');
    }

    public function isBottom(){
        return ($this->position == 'bottom');
    }
}
