<?php

namespace Urbant\CConvertBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Doctrine\ORM\EntityRepository;
use Urbant\CConvertBundle\Entity\ConvertRequest;

class ConvertRequestType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $convertRequest = new ConvertRequest();
        $builder
            ->add('site', 'entity', array(
                    'class' => 'UrbantCConvertBundle:Site',
                    'query_builder' => function (EntityRepository $repo) {
                            return $repo->createQueryBuilder('r')->orderBy('r.created', 'DESC');
                            },
                        'required' => false,
                        )
                    )
            ->add('url')
            ->add('status', 'choice', array('choices'=>$convertRequest->getStatusList()))
        ;
    }

    public function getName()
    {
        return 'urbant_cconvertbundle_convert_request_type';
    }
}
