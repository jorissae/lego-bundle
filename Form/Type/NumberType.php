<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType as ParentType;
 
class NumberType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
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
        return ParentType::class;
    }

    public function getName()
    {
        return 'lego_number';
    }

}



?>
