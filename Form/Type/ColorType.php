<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class ColorType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'format' => 'hex'
        ));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['id_conteneur'] = 'conteneur_'.$view->vars['id'];
        $view->vars['format'] = $options['format'];
        /*$view->vars['min'] = $options['min_day'];
        $view->vars['noday'] = json_encode($options['no-day']);
        $view->vars['edit_year'] = $options['edit_year'];
        $view->vars['edit_month'] = $options['edit_month'];
        $view->vars['show_diff'] = $options['show_diff'];*/
    }


    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'lle_color';
    }

}



?>
