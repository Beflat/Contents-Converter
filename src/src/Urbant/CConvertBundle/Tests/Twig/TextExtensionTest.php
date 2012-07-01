<?php

namespace Urbant\CConvertBundle\Twig;


class TextExtensionTest extends \PHPUnit_Framework_TestCase {
    
    
    public function testmbTruncateMiddle() {
        
        //15文字以上は切り詰め
        $tests = array(
            array('expected' => '123456789012345', 'input' => '123456789012345'),
            array('expected' => '123456...123456', 'input' => '1234567890123456'),
            array('expected' => '123456...567890', 'input' => '12345678901234567890'),
        );
        
        $extension = new TextExtension();
        foreach($tests as $test) {
            $this->assertEquals($test['expected'], $extension->mbTruncateMiddle($test['input'], 15));
        }
    }
    
}