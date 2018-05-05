<?php

namespace Idk\LegoBundle\Twig;

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Psr\Container\ContainerInterface;

class WidgetTwigExtension extends \Twig_Extension
{

    private $widgetChain;

    public function __construct(WidgetChain $widgetChain){
        $this->widgetChain = $widgetChain;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_use_widgets',array($this,'renderUseWidgets'), array('is_safe' => array('html'),'needs_environment' => true)),
            new \Twig_SimpleFunction('render_widget',array($this,'renderWidget'), array('is_safe' => array('html'),'needs_environment' => true)),
        );
    }

    public function renderUseWidgets(\Twig_Environment $env)
    {
        $template = $env->loadTemplate($this->widgetChain->getWidgetsTemplate());
        return $template->render(['widgets'=>$this->widgetChain->getUseWidgets()]);
    }

    public function renderWidget(\Twig_Environment $env, $widget)
    {
        $template = $env->loadTemplate($this->widgetChain->getTemplate());
        return $template->render(['widget'=>$widget]);
    }

    public function getName()
    {
        return 'widget_extension';
    }
}
