<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
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

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['full_name'] .= '[]';
        $view->vars['roles'] = $this->roles;
    }

    public function getName()
    {
        return 'lego_role';
    }
}

?>
