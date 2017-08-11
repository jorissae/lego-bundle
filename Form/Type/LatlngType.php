<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class LatlngType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'width'=>'90%',
            'height' => '400px',
            'lat' =>  47.746136,
            'lng'=>  7.337212,
            'zoom'=> 12,
            'map_type'=> 'google.maps.MapTypeId.TERRAIN',
        ));
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['id_map'] = $view->vars['id'].'_map';
        $view->vars['id_reset'] = $view->vars['id'].'_reset';
        $view->vars['id'] = $view->vars['id'];
        $view->vars['width'] = $options['width'];
        $view->vars['height'] = $options['height'];
        $view->vars['lat'] = ($view->vars['value']['lat'])? $view->vars['value']['lat']:$options['lat'];
        $view->vars['lng'] = ($view->vars['value']['lng'])? $view->vars['value']['lng']:$options['lng'];
        $view->vars['zoom'] = $options['zoom'];
        $view->vars['map_type'] = $options['map_type'];
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'lle_latlng';
    }

}



?>
