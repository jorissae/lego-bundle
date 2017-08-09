<?php
namespace Idk\LegoBundle\AdminList;

use Idk\LegoBundle\Twig\FilterTwigExtension;

/**
 * Field
 */
class Rupteur
{


    /**
     * @var string
     */
    private $name;

    private $oldValue = null;

    private $callback;

    private $footer;

    private $header;

    private $headerTemplate;

    private $footerTemplate;

    private $active;

    private $kRupt = 0;

    private $items = array();

    private $childs = array();

    private $contentOrderBy = array();

    private $key;

    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct($name, array $options = array())
    {
        $this->name     = $name;
        $this->order = (isset($options['order']))? $options['order']:'ASC';
        $this->contentOrderBy = (isset($options['content_order_by']))? $options['content_order_by']:array();
        $this->footer = (isset($options['footer']))? $options['footer']:null;
        $this->footerTemplate = (isset($options['footer_template']))? $options['footer_template']:null;
        $this->header = (isset($options['header']))? $options['header']:null;
        $this->headerTemplate = (isset($options['header_template']))? $options['header_template']:null;
        $this->callback = (isset($options['callback']))? $options['callback']:null;
        $this->active = (isset($options['active']))? $options['active']:true;
        $this->key = (isset($options['key']))? $options['key']:str_replace('.','_',$this->name);
    }

    public function addChild(Rupteur $rupteur){
        $this->childs[] = $rupteur;
    }

    public function isActive(){
        return $this->active;
    }

    public function getContentOrderBy(){
        return $this->contentOrderBy;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getOrder(){
        $order = strtoupper($this->order);
        if($order == 'ASC') return 'ASC';
        return 'DESC';
    }


    public function isBreak($adminlist,$item){
        if($this->isActive()){
            if($this->callback){
                $call = $this->callback;
                $value = $call($item);
            } else {
                $value = $adminlist->getConfigurator()->getValue($item,$this->getName());
            }
            $ret = ($this->oldValue != $value);
            $this->oldValue = $value;
            if($ret){
                $this->breakk();
            }
            if(!isset($this->items[$this->kRupt])) $this->items[$this->kRupt] = array();
            $this->items[$this->kRupt][] = $value;
            return $ret;
        }else{
            return false;
        }
    }

    public function breakk(){
        $this->kRupt++;
        foreach($this->childs as $child){
            $child->init();
        }
    }

    public function init(){
        if($this->isActive()) $this->oldValue = null;
    }


    public function getFooter(){
        $items = (isset($this->items[$this->kRupt-1]))?  $this->items[$this->kRupt-1]:array();
        $vars = array('items'=>$items);
        if($this->footer) return $this->render($this->footer,$vars);
        return null;
    }

    public function getTemplateFooter($item){
        $vars = array('item'=>$item);
        if($this->footerTemplate) return array('tmp'=>$this->footerTemplate,'vars'=>$vars);
        return null;
    }

    public function getHeader($item){
        $vars = array('item'=>$item);
        if($this->headerTemplate) return $this->render($this->headerTemplate,$vars);
        if($this->header) return $this->render($this->header,$vars);
        return null;
    }

    public function getTemplateHeader($item){
        $vars = array('item'=>$item);
        if($this->headerTemplate) return array('tmp'=>$this->headerTemplate,'vars'=>$vars);
        return null;
    }

    private function render($field,$vars){
        $loader = new \Twig_Loader_String();
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new FilterTwigExtension());
        $render = $twig->display($field,$vars);
        return $render;
    }

    public function isFooter(){
        return ($this->footer != null);
    }

    public function getClassCss(){
        return null;
    }

    public function getClassCssHeader(){
        return null;
    }

    public function getClassCssFooter(){
        return null;
    }

    public function setActive($bool){
        $this->active = $bool;
    }

    public function getKey(){
        return $this->key;
    }


}
