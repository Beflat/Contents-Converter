<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username')
            ->add('email')
//            ->add('lastLogin', 'datetime', array('required'=>false, 'read_only'=>true, 'widget'=>'single_text'))
            ->add('enabled')
            ->add('locked', null, array('required'=>false))
            ->add('roles', 'role_selector', 
                  array('required'=>true, 
                        'choices' => array(
                            'ROLE_USER' => '一般ユーザー',
                            'ROLE_ADMIN' => '管理者',
                            'ROLE_SUPER_ADMIN' => '特権管理者',
            )))

//            ->add('roles', 'choice', array('choices' => array(
//                'ROLE_USER'=>'一般ユーザー', 
//                'ROLE_ADMIN' => '管理者',
//                'ROLE_SUPER_ADMIN' => '特権管理者',
//            )))
        ;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Urbant\CConvertBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_usertype';
    }
}
