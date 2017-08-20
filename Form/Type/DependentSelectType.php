<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Idk\LegoBundle\Form\Transformer\ObjectToIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType as ParentType;

class DependentSelectType extends AbstractType
{


    private $em;

    public function __construct($em){
        $this->em = $em;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'dependent' => null,
            'route'     => null,
            'class'     => null,
            'multiple'  => false, //not work now
        ));
    }

    public function getParent()
    {
        return ParentType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach(array('route','dependent') as $k){
            if($options[$k] == null){
                throw new InvalidConfigurationException('Option "'.$k.'" must be set.');
            }
        }
        if($options['class']){
            $transformer = new ObjectToIdTransformer($this->em, $options['class']);
            $builder->addModelTransformer($transformer);
        }
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $fullName = explode('[',$view->vars['full_name']);
        $view->vars['dependent_id'] = str_replace($view->vars['name'], $options['dependent'], $view->vars['id']);
        $view->vars['input_id'] = $view->vars['id'];
        $view->vars['route'] = $options['route'];
        $view->vars['multiple'] = $options['multiple'];
    }

    public function getName()
    {
        return 'lego_dependent_select';
    }
}

?>