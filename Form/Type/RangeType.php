<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class RangeType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        if ($options['range'] == "true"|| $options['range'] == "log") {
            $builder
                    ->add('min_value', 'hidden', array('data' => $options['min']))
                    ->add('max_value', 'hidden', array('data' => $options['max']))
            ;
        } else {
            $builder->add('value', 'hidden', array('data' => $options[$options['range']]));
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'range' => true,
            'slide' => true,
            'multiplicator' => 1,
            'unite' => '',
            'min' => '1',
            'max' => '100',
            'step' => 1
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['range'] = $options['range'];
        $view->vars['min'] = $options['min'];
        $view->vars['max'] = $options['max'];
        $view->vars['unite'] = $options['unite'];
        $view->vars['step'] = $options['step'];
        $view->vars['slide'] = $options['slide'];
        $view->vars['multiplicator'] = $options['multiplicator'];
        if (!empty($view->vars['value']) && is_array($view->vars['value'])) {
            $view->vars['max_value'] = ($view->vars['value']['max_value']!=0) ? $view->vars['value']['max_value'] / $options['multiplicator'] : null;
            $view->vars['min_value'] = ($view->vars['value']['min_value']!=0 or $view->vars['value']['max_value']!=0) ? $view->vars['value']['min_value'] / $options['multiplicator'] : null;
        } else {
            $view->vars['max_value'] = null;//$options['max'];
            $view->vars['min_value'] = null;//$options['min'];
        }
    }

    public function getName() {
        return 'lle_range';
    }

}

?>
