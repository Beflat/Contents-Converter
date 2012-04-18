<?php

namespace Urbant\CConvertBundle\Scraping;

/**
 * 指定されたURLにあるHTMLを取得、DOM変換の準備やDOM変換を行うクラス
 */
class HtmlLoader {
    
    /**
     * ロードする対象のファイル。現時点ではURLのみ。
     * @var string
     */
    private $file;
    
    private $loaded = false;
    
    private $loadFailed = false;
    
    private $content;
    
    private $domDoc;
    
    private $cookie;
    
    private $error;
    
    public function __construct($file) {
        $this->file = $file;
        $this->loaded = false;
        $this->loadFailed = false;
        $this->content = '';
        $this->cookie = '';
        $this->domDoc = null;
        $this->error = '';
    }
    
    
    public function loadHtml() {
        $this->loaded = true;
        
        $url = $this->file;
        if(substr($url, 0, 2) == '//') {
            $url = 'http:' . $url;
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
        if($this->cookie != '') {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        }
        
        $this->content = curl_exec($ch);
        if(!$this->content) {
            $this->loadFailed = true;
            $this->error = curl_error($ch);
            return false;
        }
        
        //ここでUTF-8に変換しておく
        $this->content = mb_convert_encoding($this->content, 'UTF-8', 'auto');
        
        $this->loadFailed = false;
        return true;
    }
    
    
    public function getContent() {
        return $this->content;
    }
    
    
    public function getDom($version="1.0", $encoding="UTF-8") {
        if($this->domDoc != null) {
            return $this->domDoc;
        }
        $this->domDoc = new \DomDocument($version, $encoding);
        
        $this->content = preg_replace('|<head>|i', '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>', $this->content);
        
        $convertedHtml = $this->content;
        $this->stripComment($convertedHtml);
        
        @$this->domDoc->loadHTML($convertedHtml);
        return $this->domDoc;
    }
    
    public function setCookie($newCookie) {
        $this->cookie = $newCookie;
    }
    
    
    /**
     * タイトルを解析して返す。
     * @return string タイトル
     */
    public function getTitle() {
        $titleTag = $this->getDom()->getElementsByTagName('title')->item(0);
        if(!$titleTag) {
            return '';
        }
        return $titleTag->nodeValue;
    }
    
    
    public function getError() {
        return $this->error;
    }
    
    
    private function stripComment(&$content) {
        $pattern = "#(<!--(.*?)-->)#";
        preg_replace($pattern, '', $content);
    }
    
}