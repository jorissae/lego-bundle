<?php

namespace Idk\LegoBundle\Form\Type;

use Idk\LegoBundle\Form\Transformer\ArrayToJsonTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType as ParentType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JsonType extends AbstractType
{

    public function getParent()
    {
        return ParentType::class;
    }

    public function getName()
    {
        return 'lego_jsonarea';
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ArrayToJsonTransformer());
    }


    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if($view->vars['value'] && is_array(json_decode($view->vars['value'], true))) {
            $view->vars['value'] = json_encode(json_decode($view->vars['value'], true), JSON_PRETTY_PRINT);
        }
    }

}



?>
