<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

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

    const MODE_TREE = 'tree';
    const MODE_CODE = 'code';
    const MODE_FORM = 'form';

    public function getParent()
    {
        return ParentType::class;
    }

    public function getName()
    {
        return 'lego_json';
    }


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ArrayToJsonTransformer());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'mode' => self::MODE_TREE,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['mode'] = $options['mode'];
    }

}



?>
