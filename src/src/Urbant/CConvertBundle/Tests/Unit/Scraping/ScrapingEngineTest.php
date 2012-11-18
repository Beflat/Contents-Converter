<?php

namespace Urbant\CConvertBundle\Scraping;


class ScrapingEngineTest extends \PHPUnit_Framework_TestCase {

    /**
     * 相対URLのフルURL変換処理が正しく行えるかを確認する。
     */
    public function testGetCompleteUrl() {
        
        
        $scrapingEngine = new ScrapingEngine();
        
        //基準URLは"http://abc.com/test.html"とする。
        $baseUrl = 'http://abc.com/test.html';
        
        //"/abc.png"の場合
        $result = $scrapingEngine->getCompleteUrl($baseUrl, '/abc.png');
        $this->assertEquals('http://abc.com/abc.png', $result);
        
        //"/aaa/abc.png"の場合
        $result = $scrapingEngine->getCompleteUrl($baseUrl, '/aaa/abc.png');
        $this->assertEquals('http://abc.com/aaa/abc.png', $result);
        
        //"aaa/abc.png"の場合
        $result = $scrapingEngine->getCompleteUrl($baseUrl, 'aaa/abc.png');
        $this->assertEquals('http://abc.com/aaa/abc.png', $result);
        
        //"abc.png"の場合
        $result = $scrapingEngine->getCompleteUrl($baseUrl, 'abc.png');
        $this->assertEquals('http://abc.com/abc.png', $result);
        
        
        //絶対URLは"http://abc.com/test/"で始まるものとする。
        $baseUrl = 'http://abc.com/test/';
        //"/abc.png"の場合
        $result = $scrapingEngine->getCompleteUrl($baseUrl, '/abc.png');
        $this->assertEquals('http://abc.com/abc.png', $result);
        
        //"/aaa/abc.png"の場合
        $result = $scrapingEngine->getCompleteUrl($baseUrl, '/aaa/abc.png');
        $this->assertEquals('http://abc.com/aaa/abc.png', $result);
        
        //"aaa/abc.png"の場合
        $result = $scrapingEngine->getCompleteUrl($baseUrl, 'aaa/abc.png');
        $this->assertEquals('http://abc.com/test/aaa/abc.png', $result);
        
        //"abc.png"の場合
        $result = $scrapingEngine->getCompleteUrl($baseUrl, 'abc.png');
        $this->assertEquals('http://abc.com/test/abc.png', $result);
    }

}


