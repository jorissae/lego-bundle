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


class HeaderItem{

    protected $template;
    protected $templateParameters;
    protected $icon;
    protected $libelle;
    protected $label;
    protected $cssClass;


    public function __construct(array $options = []){
        $this->libelle = (isset($options['libelle']))? $options['libelle']:null;
        $this->label = (isset($options['label']))? $options['label']:null;
        $this->icon = (isset($options['icon']))? $options['icon']:null;
        $this->cssClass = (isset($options['css_class']))? $options['css_class']:null;
        $this->template = (isset($options['template']))? $options['template']:null;
        $this->templateParameters = (isset($options['template_parameters']))? $options['template_parameters']:[];
    }

    /**
     * @return mixed|null
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @param mixed|null $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @return array|mixed
     */
    public function getTemplateParameters()
    {
        return $this->templateParameters;
    }

    /**
     * @param array|mixed $templateParameters
     */
    public function setTemplateParameters($templateParameters)
    {
        $this->templateParameters = $templateParameters;
    }

    /**
     * @return mixed|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * @param mixed|null $icon
     */
    public function setIcon($icon)
    {
        $this->icon = $icon;
    }

    /**
     * @return mixed|null
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * @param mixed|null $libelle
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;
    }

    /**
     * @return mixed|null
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed|null $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
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



}