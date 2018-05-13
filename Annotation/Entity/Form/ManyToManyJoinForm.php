<?php

namespace Idk\LegoBundle\Annotation\Entity\Form;

use App\Entity\LiaisonPlayDuration;
use Idk\LegoBundle\Form\Type\EntryType;
use Idk\LegoBundle\Form\Type\ManyToManyJoinType as BaseType;
use Symfony\Component\Form\FormFactoryInterface;
use Idk\LegoBundle\Service\MetaEntityManager;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;

/**
 * @Annotation
 */
class ManyToManyJoinForm extends AbstractForm
{

    public function __construct($options){
        $options['by_reference'] = false;
        parent::__construct($options);
        $this->type = BaseType::class;
    }


}
