<?php

namespace Urbant\CConvertBundle\Scraping;


/**
 * スクレイピング処理の最中に発生するイベントにフックするためのインターフェース
 */
interface FilterInterface {
    
    /**
     * フィルター処理を実行する
     * @param string $eventName イベント名
     * @param ScrapingEngine $engine スクレイピングエンジン
     * @param array $eventArgs パラメータ
     * 
     * @return boolean 後続のフィルターも実行させたい場合はtrue
     */
    public function execute($eventName, $engine, $eventArgs);
}