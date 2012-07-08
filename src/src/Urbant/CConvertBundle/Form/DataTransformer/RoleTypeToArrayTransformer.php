<?php

namespace Urbant\CConvertBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

/**
 * Userの権限情報のModel<=>View間の型変換を行う
 */
class RoleTypeToArrayTransformer implements DataTransformerInterface {

    /**
     * ViewからのデータをModel用に変換する
     * @param type $roleTypeArray
     * @return string 
     */
    function transform($roleTypeArray) {
        if (!is_array($roleTypeArray)) {
            return '';
        }
        
        return $roleTypeArray[0];
    }

    /**
     * Model用のデータをView用に変換する
     * @param type $roleType
     * @return type 
     */
    public function reverseTransform($roleType) {
        return array($roleType);
    }

}
