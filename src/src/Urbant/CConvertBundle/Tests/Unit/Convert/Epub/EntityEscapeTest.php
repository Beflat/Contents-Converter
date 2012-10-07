<?php


namespace Urbant\CConvertBundle\Convert\Epub;


class EntityEscapeTest extends \PHPUnit_Framework_TestCase {
    
    
    /**
     */
    public function testConvert() {
        $this->assertEquals('&#x02026;', EntityEscape::convert('&hellip;'));
    }
    
    public function testReplaceAllEntities() {
        
        $document = "aiueo &hellip; aaaaa&nbsp;ccccc&quot;";
        $expected = "aiueo &#x02026; aaaaa&#x000A0;ccccc&#x00022;";
        
        EntityEscape::replaceAllEntities($document);
        $this->assertEquals($expected, $document);
    }
    
    
    
}
