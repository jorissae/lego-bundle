<?php

namespace Idk\LegoBundle\AdminList;
use Idk\LegoBundle\Twig\FilterTwigExtension;

/**
 * Field
 */
class Group
{


    /**
     * @var string
     */
    private $cols;

    private $fields;

    private $title;

    private $template;

    private $custom;

    private $customTitle;
    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct($cols,$options)
    {
        $this->cols     = $cols;
        $this->fields   = array();
        $this->title = (isset($options['title']))? $options['title']:null;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->custom = (isset($options['custom']))? $options['custom']:null;
        $this->customTitle = (isset($options['customTitle']))? $options['customTitle']:null;
    }

    public function add($field){
        $this->fields[] = $field;
    }

    public function getFields(){
        return $this->fields;
    }

    public function getCols(){
        return $this->cols;
    }

    public function getTitle(){
        return $this->title;
    }

    public function getTemplate(){
        return $this->template;
    }

    public function getCustom(){
        return $this->custom;
    }

    public function getCustomTitle(){
        return $this->customTitle;
    }

    public function getCustomRender($label,$value){
        return $this->renderer($this->getCustom(),array('label'=>$label,'value'=>$value));
    }

    public function getCustomTitleRender(){
        return $this->renderer($this->getCustomTitle(),array('title'=>$this->title));
    }

    private function renderer($html,$params){
        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new FilterTwigExtension());
        return $twig->render($html,$params);
    }


}
