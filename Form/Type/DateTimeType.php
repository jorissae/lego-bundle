<?php
namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType as ParentType;
 
class DateTimeType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'widget'=>'single_text',
            'format' => 'dd/MM/yyy HH:mm',
            'date_format' => 'dd/mm/yy',
            'time_format' => 'HH:mm',
            'min_day' => 0,
            'with_seconds' => false,
        ));
    }
    
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['id_date_picker'] = $view->vars['id'];
        $view->vars['date_format'] = $options['date_format'];
        $view->vars['time_format'] = $options['time_format'];
        $view->vars['min'] = $options['min_day'];
    }


    public function getParent()
    {
        return ParentType::class;
    }

    public function getName()
    {
        return 'lego_datetime';
    }

}



?>
