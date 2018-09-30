<?php
/**
 *  This file is part of the Lego project.
 *
 *   (c) Joris Saenger <joris.saenger@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace Idk\LegoBundle\Form;

use Idk\LegoBundle\Form\Type\RoleType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\AbstractType;

/**
 * The type for User
 */
class UserAdminType extends AbstractType
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
        $builder->add('username',null, ['label'=>'lego.form.username']);
        $builder->add('email',EmailType::class, ['label'=>'lego.form.email']);
        $builder->add('roles', RoleType::class, ['label'=>'lego.form.roles']);
        $builder->add('plainPassword', RepeatedType::class, array(
            'type' => PasswordType::class,
            'required' => false,
            'options' => array('translation_domain' => 'messages'),
            'first_options' => array('label' => 'lego.form.update_password'),
            'second_options' => array('label' => 'lego.form.password'),
            'invalid_message' => 'lego.form.error_repeat_password',
        ));
        $builder->add('enable',null,['label'=>'lego.form.enable']);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_admin_form';
    }
}
