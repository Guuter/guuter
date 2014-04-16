<?php

namespace Guuter\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class StartFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('username')
            ->remove('plainPassword')
            ->add('plainPassword', 'password', array('label' => 'form.password', 'translation_domain' => 'FOSUserBundle'))
        ;
    }

    public function getName()
    {
        return 'guuter_user_start';
    }
}
