<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class SiteType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description', 'textarea', array('required'=>false))
            ->add('cookie', 'text')
        ;
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_sitetype';
    }
}
