<?php

namespace Idk\LegoBundle\AdminList;


/**
 * Field
 */
class Field
{

    /**
     * @var string
     */
    private $header;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $sort;

    /**
     * @var null|string
     */
    private $template;

    /**
     * @var null|string
     */
    private $link_to;

    /**
     * @var bool
     */
    private $attribut_options;

    /**
     * @var bool
     */
    private $auto_display;

    /**
    * @var string
     */
    private $workflow;

    private $style;

    private $type;

    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct($name, array $options = array())
    {
        $this->name     = $name;
        $this->header = (isset($options['label']))? $options['label']:ucfirst($name);
        $this->sort = (isset($options['sort']))? $options['sort']:false;
        $this->template = (isset($options['tmp']))? $options['tmp']:null;
        $this->link_to = (isset($options['link_to']))? $options['link_to']:null;
        $this->edit_in_place = (isset($options['edit_in_place']))? $options['edit_in_place']:false;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->custom = (isset($options['custom']))? $options['custom']:null;
        $this->link_to = (is_array($this->link_to))? $this->link_to:strtolower($this->link_to);
        $this->attribut_options = (isset($options['attribut_options']))? $options['attribut_options']:false;
        $this->auto_display = (isset($options['auto_display']))? $options['auto_display']:true;
        $this->workflow = (isset($options['workflow']))? $options['workflow']:null;
        $this->style = (isset($options['style']))? $options['style']:null;
        $this->type = (isset($options['type']))? $options['type']:null;
    }

    public function set($key,$value){
        $this->$key = $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    public function getStyle(){
        return $this->style;
    }

    /**
     * @return string
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * @return bool
     */
    public function isSortable()
    {
        return $this->sort;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return string twig
     */
    public function getCustom()
    {
        return $this->custom;
    }

    /**
     * @return string
     */
    public function getLinkTo()
    {
        return $this->link_to;
    }

    public function getEditInPlaceRole(){
        if(isset($this->edit_in_place['role'])) return $this->edit_in_place['role'];
        return false;
    }

    public function getEditInPlaceJs(){
        if(isset($this->edit_in_place['js'])) return $this->edit_in_place['js'];
        return true;
    }

    public function getEditInPlacePlaceholder(){
        if(isset($this->edit_in_place['placeholder'])) return $this->edit_in_place['placeholder'];
        return null;
    }


    public function isEditInPlace($adminlist,$item){
        if(isset($this->edit_in_place['if'])){
            $m = $this->edit_in_place['if']['method'];
            $v = $this->edit_in_place['if']['value'];
            return ($item->$m() == $v);
        }
        return ($this->edit_in_place);
    }

    public function getEditInPlace(){
        return $this->edit_in_place;
    }

    public function hasEditInPlaceClass(){
        return (isset($this->edit_in_place['class']) && $this->edit_in_place['class'] != null);
    }

    public function hasEditInPlaceReload(){
        return (isset($this->edit_in_place['reload']) && $this->edit_in_place['reload'] != null);
    }

    public function getEditInPlaceClass(){
        return ($this->hasEditInPlaceClass())? $this->edit_in_place['class']:null;
    }

    public function getEditInPlaceReload(){
        return ($this->hasEditInPlaceReload())? $this->edit_in_place['reload']:'td';
    }

    public function isAttributoptions(){
        return $this->attribut_options;
    }

    public function getAutoDisplay(){
        return $this->auto_display;
    }

    public function getWorkflow(){
        return $this->workflow;
    }

    public function getStringValue(AdminList $adminlist,$item){
        if($this->getCustom()){
            return $adminlist->custom($this,$item);
        }else{
            return $adminlist->getStringValue($item, $this->getName());
        }
    }

    public function isColor(){
        return ($this->is('color'));
    }

    public function is($type){
        return (strtolower($this->type) == strtolower($type));
    }


}
