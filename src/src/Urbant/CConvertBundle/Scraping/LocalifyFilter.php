<?php

namespace Urbant\CConvertBundle\Scraping;


/**
 * DOM中の画像などのコンテンツをダウンロードして、
 * ローカルパスに置き換える
 */
class LocalifyFilter implements FilterInterface{
    
    protected $outputPath = '';
    
    /**
     * epubのコンテンツ内から三章するための、画像データなどへの相対パス
     * @var string
     */
    protected $absolutePath = '';
    
    protected $resourceSeq = 0;
    
    public function __construct($outputPath, $absolutePath=null) {
        $this->outputPath = $outputPath;
        $this->absolutePath = $absolutePath;
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
    protected function scanNodes($file, $element) {
        
//         foreach($nodeList as $element) {
            
//             if(!is_a($element, 'DOMNode')) {
//                 throw new Exception('element is not a type of DomNode. ' . var_export($element, true));
//             }
            
            //TODO: この処理は再帰的に呼び出す必要があるので別メソッドとして独立させる
            //TODO: リソースファイル名のフォーマットは外部から設定出きるように
            if($element->hasChildNodes()) {
                foreach($element->childNodes as $child) {
                    if($child->nodeType != XML_ELEMENT_NODE) {
                        continue;
                    }
                    $this->scanNodes($file, $child);
                }
            }
            if($element->nodeName == 'img') {
                
                $imgSource = $element->getAttribute('src');
                
                if(preg_match('/base64/i', $imgSource)) {
                    return;
                }
                
                //TODO:拡張子はコンテンツタイプで決定する
                $pathParts = explode('.', $imgSource);
                $ext = array_pop($pathParts);
                
                if(strlen($ext) > 4) {
                    //拡張子が5文字以上の場合は正しく判定できていない可能性がある。今の時点ではスキップする。
                    return;
                }
                
                $baseDir = $this->outputPath;
                $destImgPath = sprintf('%s/%04d.%s', $baseDir, $this->resourceSeq, $ext);
                
                //相対パスを取得する。
                //epub内では相対パス形式で参照することになるため。
                $absPath = (is_null($this->absolutePath)) ? $baseDir : $this->absolutePath;
                $absDestImgPath = sprintf('%s/%04d.%s', $absPath, $this->resourceSeq, $ext);
                
                $imgSource = $this->getCompleteUrl($file, $imgSource);
        
                $this->downloadResource($imgSource, $destImgPath);
                $element->setAttribute('src', $absDestImgPath);  //絶対パスではなく相対パスで置き換え
        
                $this->resourceSeq++;
            }
        
    }
    
    
    function downloadResource($sourceURL, $savePath) {
    
        $ch = curl_init($sourceURL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
        $response = curl_exec($ch);
        if(!$response) {
            throw new \Exception("URL:" . $sourceURL . "\n" . curl_error($ch));
        }
    
        if(!is_dir(dirname($savePath))) {
            @mkdir($savePath, 0777, true);
        }
    
        if(!file_put_contents($savePath, $response)) {
            throw new \Exception('Failed to save file: ' . $savePath);
        }
    }
    
    
    /**
    * 相対URLを絶対URLに置換して返す。
    * @param string $baseUrl 絶対URL表記の基となるURL:。(例：http://www.www.www/xxx/xxx)
    * @param string $absUrl 変換の対象となるURL (例: /aaa.php)
    */
    protected function getCompleteUrl($baseUrl, $absUrl) {
        $hostName = parse_url($baseUrl, PHP_URL_HOST);
        $dirPath = parse_url($baseUrl, PHP_URL_PATH);
    
        $result = '';
        if(substr($absUrl, 0, 7) != 'http://') {
            if(substr($absUrl, 0, 1) == '/') {
                $urlPrefix = 'http://' . $hostName;
                $result = $urlPrefix . $absUrl;
            } else {
                $urlPrefix = 'http://' . $hostName . $dirPath;
                $result = $urlPrefix . '/' . $absUrl;
            }
        } else {
            $result = $absUrl;
        }
    
        return $result;
    }
    
}

