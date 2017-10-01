<?php
namespace Idk\LegoBundle\Lib\LayoutItem;


class LabelItem{


    protected $cssClass;
    protected $libelle;


    public function __construct($libelle, array $options = []){
        $this->libelle = $libelle;
        $this->cssClass = (isset($options['css_class']))? $options['css_class']:null;
    }

    /**
     * @return mixed|null
     */
    public function getCssClass()
    {
        return $this->cssClass;
    }

    /**
     * @param mixed|null $cssClass
     */
    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
    }

    /**
     * @return mixed
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }



}