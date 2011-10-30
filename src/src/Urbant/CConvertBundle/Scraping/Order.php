<?php

namespace Urbant\CConvertBundle\Scraping;


/**
 * 任意のファイル(1つ分)に対するスクレイピング処理の設定を保持するオブジェクト
 */
class Order {
    
    protected $targetFile;
    
    //TODO: XMLか何かの設定情報を保持するようにする。
    //TODO: 今はXPathで1回フィルタするだけだが、複数回フィルタなど、より複雑な変換も出きるようにする。
    protected $xpathString;
    
    protected $filters;
    
    protected $status;
    
    protected $result;
    
    protected $error;
    
    
    const STATE_NOT_EXECUTED = 0;
    const STATE_SUCCEED = 10;
    const STATE_ERROR = 20;
    
    public function __construct() {
    }
    
    
    public function getTargetFile() {
        return $this->targetFile;
    }
    
    /**
     * スクレイピングの対象となるファイル名(URL)を設定する。
     */
    public function setTargetFile($targetFile) {
        $this->targetFile = $targetFile;
    }
    
    
    public function getXPathString() {
        return $this->xpathString;
    }
    
    
    public function setXPathString($xPathString) {
        $this->xpathString = $xPathString;
    }
    
    
    /**
     * スクレイピング処理中に任意のタイミングで呼び出される処理の追加を行う。
     * @param string イベント名（タイミング）
     * @param FilterInterface $filter
     */
    public function addFilter($eventName, FilterInterface $filter) {
        if(!isset($this->filters[$eventName])) {
            $this->filters[$eventName] = array();
        }
        $this->filters[$eventName][] = $filter;
    }
    
    
    /**
     * スクレイピング処理中に発生した様々なイベントを契機に呼び出される処理。
     * addEventListenerでフックしている場合はその処理が呼び出される。
     * イベントの一覧はScrapingEngine::On_XXを参照。
     */
    public function onEvent($eventName, $eventArgs) {
        if(!isset($this->filters[$eventName])) {
            return true;
        }
        
        foreach($this->filters[$eventName] as $filter) {
            $filter->execute($eventArgs);
        }
    }
    
    
    public function getResult() {
        return $this->result;
    }
    
    public function setResult($result) {
        $this->result = $result;
    }
    
    
    public function getError() {
        return $this->error;
    }
    
    public function setError($error) {
        $this->error = $error;
    }
    
    
    public function getStatus() {
        return $this->status;
    }
    
    public function setStatus($newState) {
        $this->status = $newState;
    }
}

