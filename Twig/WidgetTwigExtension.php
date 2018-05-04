<?php

namespace Idk\LegoBundle\Twig;

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Psr\Container\ContainerInterface;

class WidgetTwigExtension extends \Twig_Extension
{

    private $widgetsTemplate;
    private $widgetTemplate;
    private $wc;

    public function __construct(WidgetChain $wc, string $widgetsTemplate, string $widgetTemplate){
        $this->widgetsTemplate = $widgetsTemplate;
        $this->widgetTemplate = $widgetTemplate;
        $this->wc = $wc;
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
        //$template = $env->loadTemplate($this->widgetsTemplate);
        $template = $env->loadTemplate($this->wc->getWidgetsTemplate());
        return $template->render(['widgets'=>$this->wc->getUseWidgets()]);
    }

    public function renderWidget(\Twig_Environment $env, $widget)
    {
        //$template = $env->loadTemplate($this->widgetTemplate);
        $template = $env->loadTemplate($this->wc->getTemplate());
        return $template->render(['widget'=>$widget]);
    }

    public function getName()
    {
        return 'widget_extension';
    }
}
