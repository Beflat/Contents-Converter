<?php

namespace Urbant\CConvertBundle\Service;


use Urbant\CConvertBundle\Service\ImportRequestService;


class ImportRequestServiceTest extends \PHPUnit_Framework_TestCase {
    
    
    /**
     * @var ImportRequestService
     */
    protected $importService;
    
    public function setup() {
        
        $requestService = ();
        
        $this->importService = new ImportRequestService($requestService);
        
        parent::setup();
    }
    
    
    public function testgetLinesInFileList() {
        
        //空のファイルを調べた場合
        $result = $this->importService->getLinesInFileList('./fixtures/0line.txt');
        $this->assertEquals(0, $result);
        
        //5行のファイルを調べようとした場合
        $result = $this->importService->getLinesInFileList('./fixtures/5lines.txt');
        $this->assertEquals(5, $result);
        
        //存在しないファイル
        $error = null;
        try {
            $result = $this->importService->getLinesInFileList('./fixtures/5lines.txt');
        } catch(RuntimeException $e) {
            $error = $e;
        }
        $this->assertTrue($error != null);
    }
    
    
    public function testimportRequestList() {
        
        //空のファイルをインポートした場合
        
        
        //5行のファイルをインポートした場合
        
        
        //存在しないファイル
    }
}



class ConvertRequestServiceStub extends ConvertRequestService {
    
    
    
}