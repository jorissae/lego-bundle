<?php

namespace Lle\AdminListBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lle\AdminListBundle\Entity\AbstractAttribut;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lle\AdminListBundle\Form\Transformer\ObjectToIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormInterface;

class DependentSelectType extends AbstractType
{


    private $em;

    public function __construct($em){
        $this->em = $em;
    }


    public function setDefaultOptions(OptionsResolverInterface $resolver)
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
        return 'text';
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
        return 'lle_dependent_select';
    }
}

?>