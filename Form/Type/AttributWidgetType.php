<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lle\AdminListBundle\Entity\AbstractAttribut;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AttributWidgetType extends AbstractType
{

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'choices' => AbstractAttribut::getChoiceWidget()
        ));
    }

    public function getParent()
    {
        return 'choice';
    }

    public function getName()
    {
        return 'lle_attribut_widget';
    }
}

?>