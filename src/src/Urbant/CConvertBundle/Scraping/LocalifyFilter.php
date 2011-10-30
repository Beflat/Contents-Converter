<?php

namespace Urbant\CConvertBundle\Scraping;


class LocalifyFilter implements FilterInterface{
    
    protected $outputPath = '';
    
    
    protected $resourceSeq = 0;
    
    public function __construct($outputPath) {
        $this->outputPath = $outputPath;
        $this->resourceSeq = 0;
    }
    
    
    /**
     * パラメータとして渡されたDOMドキュメントを操作し、
     * @param array $eventArgs パラメータ情報を含んだ連想配列
     * 
     * $eventArgsの想定する内容は以下のとおり。
     * 
     * - 'node_list': 現在処理中のDomNodeListオブジェクト
     */
    public function execute($eventArgs) {
        
        $nodeList = $eventArgs['node_list'];
        foreach($nodeList as &$element) {
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
                $baseDir = 'OEBPS/imgs';
                $destImgPath = sprintf('%s/%s.%s', $baseDir, $resNo, $ext);
        
                if(substr($imgSource, 0, 4) != 'http') {
                    $urlPrefix = 'http://symfony.com';
                    $imgSource = $urlPrefix . $imgSource;
                }
        
                $this->downloadResource($imgSource, $destImgPath);
                $element->setAttribute('src', $destImgPath);
        
                $resNo++;
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
            mkdir($savePath, 0777, true);
        }
    
        file_put_contents($savePath, $response);
    }
    
}

