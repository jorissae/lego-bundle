<?php

namespace Lle\AdminListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class NumberType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'min'=> null,
            'max' => null,
        ));
    }
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['min'] = $options['min'];
        $view->vars['max'] = $options['max'];
    }


    public function getParent()
    {
        return 'number';
    }

    public function getName()
    {
        return 'lle_number';
    }

}



?>
