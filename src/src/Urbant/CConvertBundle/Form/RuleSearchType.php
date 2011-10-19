<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class RuleSearchType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('required'=>false))
            ->add('created_from', 'date', array('required'=>false))
            ->add('created_to', 'date', array('required'=>false))
        ;
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_rule_search_type';
    }
    
    
    public function getDefaultOptions(array $options) {
        
        return array(
            'csrf_protection' => false,
        );
    }
}
