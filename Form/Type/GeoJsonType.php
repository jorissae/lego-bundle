<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType as ParentType;
 
class GeoJsonType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
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
        $view->vars['width'] = $options['width'];
        $view->vars['height'] = $options['height'];
        $view->vars['lat'] = $options['lat'];
        $view->vars['lng'] = $options['lng'];
        $view->vars['zoom'] = $options['zoom'];
        $view->vars['map_type'] = $options['map_type'];
    }

    public function getParent()
    {
        return ParentType::class;
    }

    public function getName()
    {
        return 'lego_geojson';
    }

    public function getBlockPrefix()
    {
        return 'lego_geo_json';
    }

}



?>
