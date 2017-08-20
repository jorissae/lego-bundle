<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Idk\LegoBundle\Form\Transformer\ObjectToIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\Type\TextType as ParentType;
use Symfony\Component\OptionsResolver\OptionsResolver;
 
class AutoCompletionType extends AbstractType
{

    private $em;

    public function __construct($em){
        $this->em = $em;
    }


    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'route'=>null,
            'class'=>null,
            'params'=>array(),
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['class'] == null) {
            throw new InvalidConfigurationException('Option "class" must be set.');
        }
        if ($options['route'] == null) {
            throw new InvalidConfigurationException('Option "route" must be set.');
        }
        $transformer = new ObjectToIdTransformer($this->em, $options['class']);
        $builder->addModelTransformer($transformer);
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if($view->vars['value']) $entity = $this->em->getRepository($options['class'])->find($view->vars['value']);
        $view->vars['champ_value_label'] = $view->vars['id'].'_label';
        $view->vars['value_label'] = (isset($entity) and $entity)? $entity->__toString():null;
        $view->vars['id']    = $view->vars['id'];
        $view->vars['route'] = $options['route'];
        $view->vars['params'] = $options['params'];
        $view->vars['required'] = array_key_exists('required', $options) ? $options['required'] : false;

    }


    public function getParent()
    {
        return ParentType::class;
    }

    public function getName()
    {
        return 'lego_auto_completion';
    }

}



?>
