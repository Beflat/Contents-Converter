<?php

namespace Urbant\CConvertBundle\Form\Element;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Urbant\CConvertBundle\Form\DataTransformer\RoleTypeToArrayTransformer;

class RoleSelectorType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $transformer = new RoleTypeToArrayTransformer();
        $builder->addModelTransformer($transformer);
    }

    public function getParent() {
        return 'choice';
    }

    public function getName() {
        return 'urbant_cconvertbundle_role_selector';
    }

}
