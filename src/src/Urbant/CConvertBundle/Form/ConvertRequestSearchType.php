<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Urbant\CConvertBundle\Entity\ConvertRequest;

class ConvertRequestSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $convertRequest = new ConvertRequest();
        $builder
            ->add('url', 'text', array('required'=>false))
            ->add('rule', 'entity', array(
            	'required'=>false,
            	'class' => 'UrbantCConvertBundle:Rule',
            	'query_builder' => function ($repo) {
            	    return $repo->createQueryBuilder('r')->orderBy('r.created', 'DESC');
                }
            ))
            ->add('status', 'choice', array('choices'=>$convertRequest->getStatusList(), 'required'=>false))
            ->add('created_from', 'date', array('required'=>false))
            ->add('created_to', 'date', array('required'=>false))
        ;
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_convert_request_search_type';
    }
    
    
    public function getDefaultOptions(array $options) {
        
        return array(
            'csrf_protection' => false,
        );
    }
}
