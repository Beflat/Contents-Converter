<?php

namespace Urbant\CConvertBundle\Twig;


/**
 * 文字列操作関連のエクステンション
 */
class TextExtension extends \Twig_Extension {
    
    public function getName() {
        return 'urbant_text';
    }
    
    public function getFilters() {
        return array(
            'mb_truncate' => new \Twig_Filter_Method($this, 'mbTruncate'),
            'mb_truncate_middle' => new \Twig_Filter_Method($this, 'mbTruncateMiddle')
        );
    }
    
    /**
     * マルチバイトを意識して文字列を指定文字列で切り詰める。
     * @param string 文字列
     * @param int $length 切り詰める長さ(単位：文字)
     * @param string $suffix 切り詰めた場合に末尾に追加する文字列
     */
    public function mbTruncate($input, $length=10, $suffix = '...') {
        if(mb_strlen($input, 'utf-8') <= $length) {
            return $input;
        }
        return mb_substr($input, 0, $length, 'utf-8') . $suffix;
    }
    
    
    /**
     * マルチバイトを意識して文字列を指定文字列で切り詰める(中間を省略)。
     * @param string 文字列
     * @param int $length 切り詰める長さ(単位：文字)
     * @param string $suffix 切り詰めた場合に末尾に追加する文字列
     */
    public function mbTruncateMiddle($input, $length=10, $suffix = '...') {
        $inputLength = mb_strlen($input, 'utf-8');
        if($inputLength <= $length) {
            return $input;
        }
        
        $suffixLength = mb_strlen($suffix, 'utf-8');
        $cutLength = ($inputLength - ($length)) + $suffixLength;
        
        $offset = (int)(($inputLength - $cutLength) / 2);
        
        $result = mb_substr($input, 0,         $offset, 'utf-8') . $suffix
                . mb_substr($input, $offset+$cutLength, $inputLength, 'utf-8');
        
        return $result;
    }
    
}
