<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use App\Entity\LiaisonPlayDuration;
use Idk\LegoBundle\Form\Type\EntryType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType as BaseType;
use Symfony\Component\Form\FormFactoryInterface;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * @Annotation
 */
class CollectionForm extends AbstractForm
{

    public function __construct($options){
        $options['prototype'] = true;
        $options['allow_add'] = true;
        $options['allow_delete'] = true;
        $options['by_reference'] = false;
        parent::__construct($options);
        $this->type = BaseType::class;
    }

    public function getOptions()
    {
        $options = $this->options;
        unset($options['entity']);
        return $options;
    }

    public function addIn(FormFactoryInterface $formFactory, FormBuilderInterface &$formBuilder, MetaEntityManager $mem){
        $entryOptions = ['fields'=>[],'data_class'=>$this->options['entity'], 'block_name' => 'entry', 'allow_extra_fields'=> true, 'by_reference'=> true ];
        foreach ($mem->generateFormFields($this->options['entity']) as $field) {
            $entryOptions['fields'][] = [
                'name'    => $field->getName(),
                'type'    => $field->getType(),
                'options' => $field->getOptions(),
            ];
        }
        $this->options['entry_options'] = $entryOptions;
        $this->options['entry_type'] = EntryType::class;
        $formBuilder->add($this->getName(), $this->getType(), $this->getOptions());
    }
}
