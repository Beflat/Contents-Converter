<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RuleType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('file_path')
        ;
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_ruletype';
    }
}
