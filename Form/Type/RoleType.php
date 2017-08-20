<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Yaml\Parser;
use Symfony\Component\Form\Extension\Core\Type\TextType as ParentType;


class RoleType extends AbstractType
{

    private $role_hierarchy = null;

    public function __construct() {
        $this->role_hierarchy = $this->getRoleNames();
    }

    private function getRoleNames()
    {
        $pathToSecurity = __DIR__ . '/../../../../../../..' . '/app/config/security.yml';
        $yaml = new Parser();
        $rolesArray = $yaml->parse(file_get_contents($pathToSecurity));
        $arrayKeys = array();
        foreach ($rolesArray['security']['role_hierarchy'] as $key => $value)
        {
            //never allow assigning super admin
            if ($key != 'ROLE_SUPER_ADMIN' && $key != 'ROLE_ADMIN') {
                $arrayKeys[$key] = $this->convertRoleToLabel($key);
            }
            //skip values that are arrays --- roles with multiple sub-roles
            if (!is_array($value)) {
                if ($value != 'ROLE_SUPER_ADMIN') {
                    $arrayKeys[$value] = $this->convertRoleToLabel($value);
                }
            } else {
                if ($key != 'ROLE_ADMIN' && $key != 'ROLE_SUPER_ADMIN') {
                    foreach ($value as $sub) {
                        if ($sub != 'ROLE_ADMIN') {
                            $arrayKeys[$sub] = '* '.$this->convertRoleToLabel($sub);
                        }
                    }
                }
            }
        }
        //sort for display purposes
        //asort($arrayKeys);
        return $arrayKeys;
    }

    private function convertRoleToLabel($role)
    {
        $roleDisplay = str_replace('ROLE_', '', $role);
        $roleDisplay = str_replace('_', ' ', $roleDisplay);
        return ucwords(strtolower($roleDisplay));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array( ));
    }

    public function getParent()
    {
        return ParentType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $fullName = explode('[',$view->vars['full_name']);

        $view->vars['input_id'] = $fullName[0].'_'.$form->getName().'[]';
        $view->vars['full_name'] .= '[]';
        $view->vars['is_selected'] = function ($choice, array $values) {
                return in_array($choice, $values, true);
            };

        $choices = $this->role_hierarchy;
        
        $view->vars['choices'] = $this->role_hierarchy;
    }

    public function getName()
    {
        return 'lego_role';
    }
}

?>
