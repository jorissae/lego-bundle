<?php
namespace Idk\LegoBundle\Lib;
use Idk\LegoBundle\Twig\FilterTwigExtension;



class BreakerSeparator
{

    const HEADER = 'header';
    const FOOTER = 'footer';

    private $twig = null;
    private $template = null;
    private $title = null;
    private $cssClass = null;

    public function __construct($title, $type = self::HEADER){
        $this->title = $title;
        $this->type = ($type == self::HEADER or $type == self::FOOTER)? $type:self::HEADER;
    }

    public function getTwig()
    {
        return $this->twig;
    }

    public function setTwig($twig)
    {
        $this->twig = $twig;
    }

    public function getTemplate()
    {
        return $this->template;
    }

    public function setTemplate($template)
    {
        $this->template = $template;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getCssClass()
    {
        return $this->cssClass;
    }

    public function setCssClass($cssClass)
    {
        $this->cssClass = $cssClass;
    }

    public function isFooter(){
        return $this->type == self::FOOTER;
    }

    public function isHeader(){
        return $this->type == self::HEADER;
    }



    public function renderTwig(BreakerCollection $collection){
        $loader = new \Twig_Loader_Array();
        $twig = new \Twig_Environment($loader);
        $twig->addExtension(new FilterTwigExtension());
        $template = $twig->createTemplate($this->getTwig());
        $render = $template->render(['collection' => $collection,'title' => $this->getTitle()]);
        return $render;
    }


}
