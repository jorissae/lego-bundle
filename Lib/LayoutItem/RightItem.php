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


class RightItem{

    protected $template;
    protected $templateParameters;
    protected $icon;
    protected $title;
    protected $rightbar;


    public function __construct(array $options = []){
        $this->icon = (isset($options['icon']))? $options['icon']:null;
        $this->title = $options['title'] ?? null;
        $this->rightbar = $options['rightbar'] ?? null;
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
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed|null $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed|null
     */
    public function getRightbar()
    {
        return $this->rightbar;
    }

    /**
     * @param mixed|null $rightbar
     */
    public function setRightbar($rightbar)
    {
        $this->rightbar = $rightbar;
    }
    
    
    
    



}
