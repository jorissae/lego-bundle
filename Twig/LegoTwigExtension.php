<?php

namespace Idk\LegoBundle\Twig;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Idk\LegoBundle\Component\Component;
use Idk\LegoBundle\Annotation\Entity\Field;


class LegoTwigExtension extends \Twig_Extension
{
    /**
     * @var \Twig_Environment
     */
    protected $environment;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }


    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_field_value',array($this,'renderFieldValue'), array('is_safe' => array('html'),'needs_environment' => true))
        ];
    }


    public function renderFieldValue(\Twig_Environment $env, Component $component, Field $field, $item)
    {
        $template = $env->loadTemplate("IdkLegoBundle:LegoTwigExtension:_field_value.html.twig");
        return $template->render(array(
            'field'        => $field,
            'configurator'      => $component->getConfigurator(),
            'item'     => $item,
            'string_value' => $field->getStringValue($component->getConfigurator(), $item),
            'real_value' => $field->getValue($component->getConfigurator(), $item)
        ));
    }


    public function getName()
    {
        return 'lego_twig_extension';
    }

}
