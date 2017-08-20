<?php

namespace {{ namespace }}\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * The type for {{ entity_class }}
 */
class {{ entity_class }}LegoType extends AbstractType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @see FormTypeExtensionInterface::buildForm()
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    {% for fieldName, data in fields %}
    $builder->add('{{ fieldName }}',{% if data.formType %} '{{data.formType}}' {% else %} null {% endif %},['label' =>  '{{ data.fieldTitle }}']);
    {% endfor %}}

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */['actions'=>
    public function getName()
    {
        return '{{ entity_class|lower }}_form';
    }['actions'=>
}
