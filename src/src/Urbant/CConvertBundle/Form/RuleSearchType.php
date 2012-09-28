<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RuleSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array('required'=>false))
            ->add('site', 'entity', array(
                    'class' => 'UrbantCConvertBundle:Site',
                    'query_builder' => function($repo) {
                        return $repo->createQueryBuilder('s')->orderBy('s.created', 'DESC');
                        },
                    'required' => false
               ))
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
