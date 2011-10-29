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
            ->add('site', 'entity', array(
                    'class' => 'UrbantCConvertBundle:Site',
                    'query_builder' => function($repo) {
                            return $repo->createQueryBuilder('s')->orderBy('s.created', 'DESC');
                        },
                    'required' => false
                ))
            ->add('file_path')
        ;
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_ruletype';
    }
}
