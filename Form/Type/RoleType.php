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

use Idk\LegoBundle\Form\Transformer\DistinctArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType as ParentType;


class RoleType extends AbstractType
{

    private $roles = [];

    public function __construct($rolesHierarchy) {
        $this->roles = $rolesHierarchy;
    }


    public function getParent()
    {
        return ParentType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new DistinctArrayTransformer());
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['full_name'] .= '[]';
        $view->vars['roles'] = empty($this->roles) ?  ['ROLE_USER'=>'ROLE_USER','ROLE_ADMIN'=>'ROLE_ADMIN']:$this->roles;
    }

    public function getName()
    {
        return 'lego_role';
    }
}

?>
