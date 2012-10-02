<?php

namespace Urbant\CConvertBundle\Service;

use Urbant\CConvertBundle\Entity\ConvertRequest;
use Urbant\CConvertBundle\Model\ConvertRequest\RequestListFileIterator;

/**
 * リクエストログのインポートに関する処理を記述するクラス
 */
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

        $iterator = new RequestListFileIterator($file);
        foreach($iterator as $request) {

            if(trim($request->getUrl()) === '' || $this->isAlreadyExists($request->getUrl())) {
                if($callback != null) {
                    $callback($request->getUrl(), self::RESULT_SKIP);
                }
                continue;
            }
            $this->requestService->saveRequest($request);
            if($callback != null) {
                $callback($request->getUrl(), self::RESULT_IMPORT);
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
        return $count;
    }

    protected function isAlreadyExists($url) {
        return false;
    }
}