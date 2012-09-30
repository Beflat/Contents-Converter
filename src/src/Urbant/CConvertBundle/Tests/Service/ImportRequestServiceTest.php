<?php

namespace Urbant\CConvertBundle\Service;


use Urbant\CConvertBundle\Service\ImportRequestService;


class ImportRequestServiceTest extends \PHPUnit_Framework_TestCase {
    
    
    /**
     * @var ImportRequestService
     */
    protected $importService;
    
    public function setup() {
        
        parent::setup();
    }
    
    
    /**
     * @covers getLinesInFileList
     */
    public function testEmptyFileShouldReturn1Line() {
        //空のファイルを調べた場合
        $convertRequestServiceStub = $this->getMock('Urbant\CConvertBundle\Service\ConvertRequestService', null, array(), '', false);
        $importService = new ImportRequestService($convertRequestServiceStub);
        $result = $importService->getLinesInFileList(dirname(__FILE__) . '/fixtures/0line.txt');
        $this->assertEquals(1, $result);
    }
    
    
    /**
     * @covers getLinesInFileList
     */
    public function test5LinesFileShouldReturn5Lines() {
        //5行のファイルを調べようとした場合
        $convertRequestServiceStub = $this->getMock('Urbant\CConvertBundle\Service\ConvertRequestService', array(), array(), '', false);
        $importService = new ImportRequestService($convertRequestServiceStub);
        $result = $importService->getLinesInFileList(dirname(__FILE__) . '/fixtures/5lines.txt');
        $this->assertEquals(5, $result);
    }
    
    
    public function testErrorOnNonExistFile() {
        //存在しないファイル
        $convertRequestServiceStub = $this->getMock('Urbant\CConvertBundle\Service\ConvertRequestService', array(), array(), '', false);
        $importService = new ImportRequestService($convertRequestServiceStub);
        
        $error = null;
        try {
            $result = $importService->getLinesInFileList(dirname(__FILE__) . '/fixtures/not_found.txt');
        } catch(\RuntimeException $e) {
            $error = $e;
        }
        
        $this->assertInstanceOf('RuntimeException', $error);

    }
    
    
    /**
     * @covers importRequestList
     */
    public function testEmptyFileShouldNotImportAnyData() {
        
        $convertRequestServiceMock = $this->getMock('Urbant\CConvertBundle\Service\ConvertRequestService', array(), array(), '', false);
        $convertRequestServiceMock->expects($this->never())
            ->method('saveRequest')
            ->will($this->returnValue(true));
        $convertRequestServiceMock->expects($this->any())
            ->method('isAlreadyExists')
            ->will($this->returnValue(false));
        
        $count = array(
            ImportRequestService::RESULT_IMPORT => 0,
            ImportRequestService::RESULT_SKIP => 0,
        );
        
        $importService = new ImportRequestService($convertRequestServiceMock);
        $importService->importRequestList(dirname(__FILE__) . '/fixtures/empty.txt', function($url, $result) use (&$count){
            $count[$result]++;
        });
        
        $this->assertEquals(0, $count[ImportRequestService::RESULT_IMPORT], 'Imported lines');
        $this->assertEquals(4, $count[ImportRequestService::RESULT_SKIP], 'Skipped lines');
    }
    
    
    /**
     * @covers importRequestList
     */
    public function test5LinesFileShouldImport5Requests() {
        
        $convertRequestServiceMock = $this->getMock('Urbant\CConvertBundle\Service\ConvertRequestService', array(), array(), '', false);
        $convertRequestServiceMock->expects($this->exactly(5))
            ->method('saveRequest')
            ->will($this->returnValue(true));
         $convertRequestServiceMock->expects($this->any())
             ->method('isAlreadyExists')
             ->will($this->returnValue(false));
        
        $count = array(
            ImportRequestService::RESULT_IMPORT => 0,
            ImportRequestService::RESULT_SKIP => 0,
        );
        
        $importService = new ImportRequestService($convertRequestServiceMock);
        $importService->importRequestList(dirname(__FILE__) . '/fixtures/5lines.txt', function($line, $result) use(&$count) {
            $count[$result]++;
        });
        
        $this->assertEquals(5, $count[ImportRequestService::RESULT_IMPORT], 'Imported lines');
        $this->assertEquals(0, $count[ImportRequestService::RESULT_SKIP], 'Skipped lines');
    }
    
    
    /**
     * @covers importRequestList
     */
    public function testNonExistFileShouldRaiseError() {
        $convertRequestServiceMock = $this->getMock('Urbant\CConvertBundle\Service\ConvertRequestService', array(), array(), '', false);
        $convertRequestServiceMock->expects($this->never())
            ->method('saveRequest')
            ->will($this->returnValue(true));
        
        $importService = new ImportRequestService($convertRequestServiceMock);
        $error = false;
        try {
            $importService->importRequestList(dirname(__FILE__) . '/fixtures/not_found.txt');
        } catch(\RuntimeException $e) {
            $error = $e;
        }
        $this->assertInstanceOf('RuntimeException', $error);
    }
}


