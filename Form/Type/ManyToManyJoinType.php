<?php

namespace Idk\LegoBundle\Form\Type;

use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType as ParentType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\Form\Extension\Core\EventListener\ResizeFormListener;

class ManyToManyJoinType extends AbstractType
{

    private $mem;

    public function __construct(MetaEntityManager $mem)
    {
        $this->mem = $mem;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $prototypeOptions = array_replace(array(
            'required' => $options['required'],
            'label' => $options['prototype_name'].'label__'
        ), $options['entry_options']);

        if (null !== $options['prototype_data']) {
            $prototypeOptions['data'] = $options['prototype_data'];
        }
        $prototype = $builder->create($options['prototype_name'], $options['entry_type'], $prototypeOptions);
        $builder->setAttribute('prototype', $prototype->getForm());




        $resizeListener = new ResizeFormListener(
            $options['entry_type'],
            $options['entry_options'],
            $options['allow_add'],
            $options['allow_delete'],
            $options['delete_empty']
        );

        $builder->addEventSubscriber($resizeListener);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $entryOptionsNormalizer = function (Options $options, $value) {
            if(!isset($value['fields'])) {
                $value = ['fields' => [], 'data_class' => $options['entity'], 'block_name' => 'entry', 'allow_extra_fields' => true, 'by_reference' => true];
                foreach ($this->mem->generateFormFields($options['entity']) as $field) {
                    $value['fields'][] = [
                        'name' => $field->getName(),
                        'type' => $field->getType(),
                        'options' => $field->getOptions(),
                    ];
                }
            }
            $value['block_name'] = 'entry';

            return $value;
        };


        $resolver->setDefaults(array(
            'allow_add' => true,
            'allow_delete' => true,
            'prototype'=> true,
            'prototype_data' => null,
            'prototype_name' => '__name__',
            'entry_type' => EntryType::class,
            'entry_options'=> [],
            'delete_empty' => false,
            'allow_extra_fields' => true,
        ));

        $resolver->setNormalizer('entry_options', $entryOptionsNormalizer);
        $resolver->setAllowedTypes('delete_empty', array('bool', 'callable'));
        $resolver->setRequired('entity');
    }


    public function getParent()
    {
        return CollectionType::class;
    }

    public function getName()
    {
        return 'lego_many_to_many_join';
    }

    public function getBlockPrefix()
    {
        return 'lego_many_to_many_join';
    }

}



?>
