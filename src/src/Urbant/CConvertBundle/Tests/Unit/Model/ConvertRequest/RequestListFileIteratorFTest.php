<?php

namespace Urbant\CConvertBundle\Model\ConvertRequest;


class RequestListFileIteratorTest extends \PHPUnit_Framework_TestCase {
    
    private $fixtureDir = '';
    
    
    public function setup() {
        $this->fixtureDir = dirname(__FILE__) . '/fixtures';
    }
    
    /**
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::openFile
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::current
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::valid
     */
    public function testEmptyFileShouldNotGenerateAnyData() {
        $iterator = new RequestListFileIterator($this->fixtureDir . '/0line.txt');
        $count = 0;
        foreach($iterator as $key=>$value) {
            $count++;
        }
        $this->assertEquals(0, $count, 'With foreach.');
        
        $iterator = new RequestListFileIterator($this->fixtureDir . '/0line.txt');
        $count = 0;
        while($iterator->valid()) {
            $count++;
            $iterator->next();
        }
        $this->assertEquals(0, $count, 'With while loop.');
        
    }
    
    
    /**
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::openFile
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::current
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::valid
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::next
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::key
     */
    public function test5LinesFileShouldGenerate5Data() {
        $iterator = new RequestListFileIterator($this->fixtureDir . '/5lines.txt');
        $count = 0;
        foreach($iterator as $key=>$value) {
            $count++;
            switch($key) {
                case 1: $this->assertEquals('1st line', $value->getUrl()); break;
                case 2: $this->assertEquals('2nd line', $value->getUrl()); break;
                case 3: $this->assertEquals('3rd line', $value->getUrl()); break;
                case 4: $this->assertEquals('4th line', $value->getUrl()); break;
                case 5: $this->assertEquals('5th line', $value->getUrl()); break;
                default:
                    throw new Exception('Invalid key id.');
            }
        }
        $this->assertEquals(5, $count, 'With foreach.');
        
        
        $iterator = new RequestListFileIterator($this->fixtureDir . '/5lines.txt');
        
        $count = 0;
        while($iterator->valid()) {
            $count++;
            $iterator->next();
        }
        $this->assertEquals(5, $count, 'With while loop.');
    }
    
    
    /**
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::current
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::valid
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::rewind
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::openFile
     * 
     */
    public function testCurrentMethodReturns1StLineDataWhen1StCall() {
        
        //ループなし
        $iterator = new RequestListFileIterator($this->fixtureDir . '/5lines.txt');
        $result = $iterator->current();
        $this->assertEquals('1st line', $result->getUrl(), 'Without loop.');
        
        //ループあり
        $iterator = new RequestListFileIterator($this->fixtureDir . '/5lines.txt');
        while($iterator->valid()) {
            $result = $iterator->current();
            $this->assertEquals('1st line', $result->getUrl(), 'With while loop.');
            break;
        }
        
        $iterator = new RequestListFileIterator($this->fixtureDir . '/5lines.txt');
        foreach($iterator as $key=>$result) {
            $result = $iterator->current();
            $this->assertEquals('1st line', $result->getUrl(), 'With foreach loop.');
            break;
        }
        
        //rewind直後
        $iterator = new RequestListFileIterator($this->fixtureDir . '/5lines.txt');
        $iterator->next();
        $iterator->next();
        $iterator->next();
        $iterator->rewind();
        $result = $iterator->current();
        $this->assertEquals('1st line', $result->getUrl(), 'Post rewind.');
    }
    
    
    /**
     * @covers Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator::openFile
     */
    public function testNonExistFileShouldRaiseError() {
        $iterator = new RequestListFileIterator($this->fixtureDir . '/not_found.txt');
        
        $error = null;
        try {
            $iterator->valid();
        } catch(\RuntimeException $e) {
            $error = $e;
        }
        $this->assertInstanceOf('RuntimeException', $error);
    }
}
