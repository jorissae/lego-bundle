<?php

namespace Idk\LegoBundle\Twig;

use Idk\LegoBundle\Service\Tag\WidgetChain;
use Psr\Container\ContainerInterface;

class WidgetTwigExtension extends \Twig_Extension
{

    private $manager;

    public function __construct(){
        //$this->manager = $container->get('lego.widget.chain');
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
        $template = $env->loadTemplate($this->manager->getWidgetsTemplate());
        return $template->render(['widgets' => []]);
    }

    public function renderWidget(\Twig_Environment $env, $widget)
    {
        $template = $env->loadTemplate($this->manager->getTemplate());
        $widget = $this->manager->get($widget);
        $params = array_merge($widget->getParams(),['widget'=>$widget]);
        return $template->render($params);
    }

    public function getName()
    {
        return 'widget_extension';
    }
}
