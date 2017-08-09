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
 
class EntityHiddenType extends AbstractType
{
    private $em;

    public function __construct($em){
        $this->em = $em;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'class'     => null,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        foreach(array('class') as $k){
            if($options[$k] == null){
                throw new InvalidConfigurationException('Option "'.$k.'" must be set.');
            }
        }
        $transformer = new ObjectToIdTransformer($this->em, $options['class']);
        $builder->addModelTransformer($transformer);
    }

    public function getParent()
    {
        return 'hidden';
    }

    public function getName()
    {
        return 'lle_entity_hidden';
    }
}