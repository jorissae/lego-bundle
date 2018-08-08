<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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