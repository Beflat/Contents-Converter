<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Urbant\CConvertBundle\Entity\Content;

class ContentSearchType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $content = new Content();
        
        $builder
            ->add('rule', 'entity', array('class'=>'UrbantCConvertBundle:Rule',
                'query_builder' => function($repo) {
                        return $repo->createQueryBuilder('r')->orderBy('r.created', 'DESC');
                    },
            		'required'=>false))
            ->add('status', 'choice', array('choices' => $content->getStatusList(), 'required'=>false))
            ->add('created_from', 'date', array('required'=>false))
            ->add('created_to', 'date', array('required'=>false))
        ;
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_content_search_type';
    }
    
    
    public function getDefaultOptions(array $options) {
        
        return array(
            'csrf_protection' => false,
        );
    }
}
