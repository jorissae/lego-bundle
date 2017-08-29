<?php
namespace Idk\LegoBundle\Annotation\Entity;

use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Idk\LegoBundle\Twig\FilterTwigExtension;

/**
 * @Annotation
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
    private $path;

    private $style;

    private $type;

    private $twig;

    private $image;

    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct(array $options = [])
    {
        $this->header = (isset($options['label']))? $options['label']:null;
        $this->sort = (isset($options['sort']))? $options['sort']:false;
        $this->template = (isset($options['tmp']))? $options['tmp']:null;
        $this->path = (isset($options['path']))? $options['path']:null;
        $this->edit_in_place = (isset($options['edit_in_place']))? $options['edit_in_place']:false;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->twig = (isset($options['twig']))? $options['twig']:null;
        $this->style = (isset($options['style']))? $options['style']:null;
        $this->type = (isset($options['type']))? $options['type']:null;
        $this->image = (isset($options['image']))? $options['image']:null;
        if($this->image) $this->type = 'image';
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

    public function setName($name){
        $this->name = $name;
        $this->header = ($this->header)? $this->header:$this->name;
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
    public function getTwig()
    {
        return $this->twig;
    }

    /**
     * @return string
     */
    public function getPath(){
        return $this->path;
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


    public function isEditInPlace($item){
        if(isset($this->edit_in_place['if'])){
            $m = $this->edit_in_place['if']['method'];
            $v = $this->edit_in_place['if']['value'];
            return ($item->$m() == $v);
        }
        return $this->edit_in_place;
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


    public function getStringValue(AbstractConfigurator $configurator,$entity){
        if($this->getTwig()){
            return $this->generateTwigValue($configurator, $entity);
        }else{
            return $configurator->getStringValue($entity, $this->getName());
        }
    }

    public function isColor(){
        return ($this->is('color'));
    }

    public function isFile(){
        return ($this->is('file'));
    }

    public function isImage(){
        return ($this->is('image'));
    }

    public function is($type){
        return (strtolower($this->type) == strtolower($type));
    }

    public function getImage(){
        return $this->image;
    }

    public function generateTwigValue(AbstractConfigurator $configurator, $entity){
            $value = $configurator->getValue($entity, $this->getName());
            $loader = new \Twig_Loader_Array();
            $twig = new \Twig_Environment($loader);
            $twig->addExtension(new FilterTwigExtension());
            $template = $twig->createTemplate($this->getTwig());
            $render = $template->render(['entity' => $entity,'label' => $this->getHeader(), 'value' => $value]);
            return $render;
    }


}
