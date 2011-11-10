<?php

namespace Urbant\CConvertBundle\Scraping;


/**
 * DOM中の画像などのコンテンツをダウンロードして、
 * ローカルパスに置き換える
 */
class LocalifyFilter implements FilterInterface{
    
    protected $outputPath = '';
    
    
    protected $resourceSeq = 0;
    
    public function __construct($outputPath) {
        $this->outputPath = $outputPath;
        $this->resourceSeq = 0;
    }
    
    
    /**
     * パラメータとして渡されたDOMドキュメントを操作し、
     * 
     * @param string $name イベント名
     * @param ScrapingEngine $engine スクレイピングエンジン
     * @param array $eventArgs パラメータ情報を含んだ連想配列
     * 
     * $eventArgsの想定する内容は以下のとおり。
     * 
     * - 'node_list': 現在処理中のDomNodeListオブジェクト
     */
    public function execute($name, $engine, $eventArgs) {
        
        if($name != 'on_scraping_done') {
            return true;
        }
        
        $this->scanNodes($eventArgs['file'], $eventArgs['node_list']);
        
        return true;
    }
    
    
    /**
     * 
     * @param unknown_type $nodeList
     */
    protected function scanNodes($file, $nodeList) {
        
        //URLのホスト名を抜き出す
        $hostName = parse_url($file, PHP_URL_HOST);
        
        foreach($nodeList as &$element) {
            
            if(!is_a($element, 'DOMNode')) {
                throw new Exception('element is not a type of DomNode. ' . var_export($element, true));
            }
            
            //TODO: この処理は再帰的に呼び出す必要があるので別メソッドとして独立させる
            //TODO: リソースファイル名のフォーマットは外部から設定出きるように
            if($element->hasChildNodes()) {
                foreach($element->childNodes as $child) {
                    if($child->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }
                    $this->scanResource($child);
                }
            }
            if($element->nodeName == 'img') {
        
                $imgSource = $element->getAttribute('src');
                //TODO:拡張子はコンテンツタイプで決定する
                $pathParts = explode('.', $imgSource);
                $ext = array_pop($pathParts);
                $baseDir = $this->outputPath;
                $destImgPath = sprintf('%s/%s.%s', $baseDir, $this->resourceSeq, $ext);
        
                if(substr($imgSource, 0, 4) != 'http') {
                    $urlPrefix = 'http://' . $hostName;
                    $imgSource = $urlPrefix . $imgSource;
                }
        
                $this->downloadResource($imgSource, $destImgPath);
                $element->setAttribute('src', $destImgPath);
        
                $this->resourceSeq++;
            }
        }
        
    }
    
    
    function downloadResource($sourceURL, $savePath) {
    
        $ch = curl_init($sourceURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        if(!$response) {
            throw new Exception("URL:" . $sourceURL . "\n" . curl_error($ch));
        }
    
        if(!is_dir(dirname($savePath))) {
            @mkdir($savePath, 0777, true);
        }
    
        if(!file_put_contents($savePath, $response)) {
            throw new Exception('Failed to save file: ' . $savePath);
        }
    }
    
}

