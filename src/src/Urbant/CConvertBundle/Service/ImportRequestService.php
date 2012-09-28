<?php

namespace Urbant\CConvertBundle\Service;

/**
 * リクエストログのインポートに関する処理を記述するクラス
 */

use Urbant\CConvertBundle\Entity\ConvertRequest;

class ImportRequestService {
    
    
    const RESULT_SKIP   = 'SKIP';
    const RESULT_IMPORT = 'IMPORT';
    
    /**
     * @var ConvertRequestService
     */
    protected $requestService;
    
    public function __construct(ConvertRequestService $requestService) {
        $this->requestService = $requestService;
    }
    
    
    /**
     * 改行区切りでURLが列挙されたファイルをインポートする。
     * @param string $file インポートするファイル
     * @param Closure $callback 各行をインポートした際に呼び出される処理
     * @throws \RuntimeException
     * 
     * $callback = function($url, $result);
     */
    public function importRequestList($file, $callback=null) {
        if(!is_file($file)) {
            throw new \RuntimeException('ファイルが見つかりません: ' . $file);
        }
        
        $fp = fopen($file, 'r');
        while(!feof($fp)) {
            $url = fgets($fp, 2048);
            
            if($this->isAlreadyExists($url)) {
                if($callback != null) {
                    $callback($url, self::RESULT_SKIP);
                }
                continue;
            }
            
            $request = new ConvertRequest();
            $request->setUrl($url);
            $this->requestService->saveRequest($request);
            if($callback != null) {
                $this->callback($url, self::RESULT_IMPORT);
            }
        }
    }
    
    
    public function getLinesInFileList($file) {
        if(!is_file($file)) {
            throw new \RuntimeException('ファイルが見つかりません: ' . $file);
        }
        $fp = fopen($file, 'r');
        $count = 0;
        while(!feof($fp)) {
            fgets($fp, 2048);
            $count++;
        }
        fclose($fp);
        return $fp;
    }
    
    protected function isAlreadyExists($url) {
    }
}