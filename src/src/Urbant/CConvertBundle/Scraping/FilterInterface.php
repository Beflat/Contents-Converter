<?php

namespace Urbant\CConvertBundle\Scraping;


/**
 * スクレイピング処理の最中に発生するイベントにフックするためのインターフェース
 */
interface FilterInterface {
    
    public function execute($eventArgs);
}