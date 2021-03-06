<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class RuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
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
            ->add('xpath')
            ->add('paginate_xpath', 'text', array('required'=>false))
            ->add('matching_rule', 'text')
            ->add('file_path')
            ->add('cookie', 'textarea', array('required'=>false))
        ;
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_ruletype';
    }
}
