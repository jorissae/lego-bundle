<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Annotation\Entity;

use Idk\LegoBundle\Configurator\AbstractConfigurator;
use Idk\LegoBundle\Lib\ViewParams;
use Idk\LegoBundle\Twig\FilterTwigExtension;
use Twig\Loader\ArrayLoader;
use Twig\Template;

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

    private $options;

    private $cssClass;

    /**
     * @param string $name     The name
     * @param string $header   The header
     * @param bool   $sort     Sort or not
     * @param string $template The template
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    public function override(Field $field){
        $options = array_merge($this->getOptions(),$field->getOptions());
        $this->setOptions($options);
        return $this;
    }

    public function getOptions(){
        return $this->options;
    }

    public function setOptions($options){
        $this->options = $options;
        $this->header = (isset($options['label']))? $options['label']:null;
        $this->sort = (isset($options['sort']))? $options['sort']:false;
        $this->template = (isset($options['tmp']))? $options['tmp']:null;
        $this->path = (isset($options['path']))? $options['path']:null;
        $this->edit_in_place = (isset($options['edit_in_place']))? $options['edit_in_place']:false;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->cssClass = (isset($options['css_class']))? $options['css_class']:null;
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

    public function getId(){
        return str_replace('.','_',$this->getName());
    }

    public function setName($name){
        $this->name = $name;
        $this->header = ($this->header)? $this->header:'field.'.$this->name;
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

    public function setHeader($header){
        return $this->header = $header;
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
        return ($this->hasEditInPlaceReload())? $this->edit_in_place['reload']:'value';
    }


    public function getStringValue(AbstractConfigurator $configurator,$entity){
        if($this->getTwig()) {
            return $this->generateTwigValue($configurator, $entity);
        } else if($this->getTemplate()){
            return $this->generateTemplateValue($configurator, $entity);
        }else{
            return $configurator->getStringValue($entity, $this->getName());
        }
    }

    public function getValue(AbstractConfigurator $configurator,$entity){
        return $configurator->getValue($entity, $this->getName());
    }

    public function setValue(AbstractConfigurator $configurator,$entity, $value){
        if(strrpos($this->getName(), '.')){
            $toPersist = $configurator->getValue($entity, substr($this->getName(), 0, (strrpos($this->getName(), '.'))));
            $method = 'set'.substr($this->getName(), (strrpos($this->getName(), '.') + 1));
        }else{
            $toPersist = $entity;
            $method = 'set'.$this->getName();
        }
        $toPersist->$method($value);
        return $toPersist;
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
        $img = $this->image;
        $img['width'] = (isset($img['width']) && $img['width'])? $img['width']:'50px';
        return $img;
    }


    public function generateTwigValue(AbstractConfigurator $configurator, $entity){
        $value = $this->getValue($configurator, $entity);
        $twig = new \Twig\Environment(new ArrayLoader());
        $template = $twig->createTemplate($this->getTwig());
        return $template->render(['view' => $this->getViewParams($configurator, $entity, $value)]);
    }

    public function generateTemplateValue(AbstractConfigurator $configurator, $entity){
        $value = $this->getValue($configurator, $entity);
        return $configurator->getConfiguratorBuilder()->render($this->getTemplate(), ['view' => $this->getViewParams($configurator, $entity, $value)]);
    }

    public function getViewParams($configurator, $entity, $value){
        /*
            WARNING: getStringValue --> generate*Value --> getViewParams
            Do not ever call self::getStringValue here because call infiny loop
            setField is not use because in the case The final user could call view.field.stringValue and call an infiny loop
         */
        $vp = new ViewParams();
        $vp->setEntity($entity);
        $vp->setConfigurator($configurator);
        $vp->setValue($value);
        $vp->setLabel($this->getHeader());
        return $vp;
    }

    public function getSort(){
        return $this->sort;
    }

    public function getCssClass(){
        return $this->cssClass;
    }


}
