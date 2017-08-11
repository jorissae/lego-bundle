<?php

namespace Idk\LegoBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lle\AdminListBundle\Entity\AbstractAttribut;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Lle\AdminListBundle\Form\Transformer\ObjectToIdTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Yaml\Parser;


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

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            ));
    }

    public function getParent()
    {
        return 'text';
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
        return 'lle_role';
    }
}

?>
