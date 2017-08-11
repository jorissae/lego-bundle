<?php
namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TimeType extends AbstractType
{
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'widget'=>'single_text',
            'format' => 'HH:mm',
            'time_format' => 'HH:mm',
            'with_seconds' => false,
            'min_time'     => null,
            'max_time'     => null,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['id_time_picker'] = $view->vars['id'];
        $view->vars['time_format'] = $options['time_format'];
        $view->vars['min_time'] = $options['min_time'];
        $view->vars['max_time'] = $options['max_time'];
    }


    public function getParent()
    {
        return 'time';
    }

    public function getName()
    {
        return 'lle_time';
    }

}



?>
