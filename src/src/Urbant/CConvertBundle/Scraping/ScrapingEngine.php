<?php

namespace Urbant\CConvertBundle\Scraping;


class ScrapingEngine {
    
    protected $orders;
    
    
    protected $outputDir;
    
    
    /**
     * 変換結果のDomDocument
     * @var \DomDocument
     */
    protected $resultDoc;
    
    
    protected $cookie = '';
    
    public function __construct() {
        
        $this->orders = array();
        
        $this->resultDoc = new \DOMDocument('1.0','UTF-8');
    }
    
    
    public function setOutputPath($path) {
        $this->outputDir = $path;
    }
    
    /**
     * スクレイピング対象の情報を追加する
     * @param Order $order 対象となるファイルの設定情報を含んだオブジェクト(どの部分を抜き出すかなど)
     */
    public function addOrder(Order $order) {
        $this->orders[] = $order;
    }
    
    /**
     * コンテンツの抽出処理を実行する。
     */
    public function execute() {
        
        $bodyTag = null;
        
        foreach($this->orders as $idx=>$order) {
            
            try {
                //指定されたファイルを開いてHTMLをロードし、
                //Orderで指定されたXPath設定で絞り込みをかけ、
                //その結果をOrderに返す。
                //TODO: 最終的には、XPathで絞り込む以外の操作も
                //Orderで指定すれば出きるようにする。どのようにコンテンツを
                //加工するかについてもOrderが設定を持つようにする。
                
                //TODO: URL以外のパス情報も扱えるように
                //TODO:異常系の実装。解析できなかったなど
                $file = $order->getTargetFile();
                $xPathString = $order->getXPathString();
                $htmlText = $this->loadContentText($file);
                
                //XPathで絞り込みを行う
                $domDoc = new \DOMDocument('1.0', 'UTF-8');
                @$domDoc->loadHtml($htmlText);
                
                //最初の１回だけ、結果格納用ドキュメントの初期化処理を呼び出す
                //HTMLタグ、HEADタグなどの初期化
                if($idx == 0) {
                    $this->initResultDocument($domDoc);
                    
                    //bodyタグを取得
                    $bodyTag = $this->resultDoc->getElementsByTagName('body')->item(0);
                }
                
                $scrapedNodes = $this->extractContent($domDoc, $xPathString);
                
                foreach($scrapedNodes as $scrapedNode) {
                    $order->onEvent('on_scraping_done', $this, array('file' => $file, 'node_list'=>$scrapedNode));
                }
                
                $content = '';
                foreach($scrapedNodes as $entry) {
                    $bodyTag->appendChild($this->resultDoc->importNode($entry, true));
                    //$this->resultDoc->importNode($entry, true);
                }
                
                $order->setStatus(Order::STATE_SUCCEED);
            } catch(Exception $e) {
                $order->setError($e->getMessage());
                $order->setStatus(Order::STATE_ERROR);
            }
        }
        //TODO:全オーダーが正常に完了した事を確認する。
        //TODO:ファイルへの保存を行うか、Engineの利用側でOrderの一覧にする手段を提供する
    }
    
    
    public function getResult() {
        return $this->resultDoc->saveXML();
    }
    
    
    public function getOrders() {
        return $this->orders;
    }
    
    
    public function hasError() {
    }
    
    public function hasWarning() {
    }
    
    
    public function getJoinedError() {
    }
    
    public function getJoinedWarning() {
    }
    
    
    /**
     * タイトルを解析して返す。
     * @return string タイトル
     * 
     * 
     */
    public function getTitle() {
        $titleTag = $this->resultDoc->getElementsByTagName('title')->item(0);
        if(!$titleTag) {
            return '';
        }
        return $titleTag->nodeValue;
    }
    
    
    /**
    * 指定されたURLに対してXPathを適用して絞り込んだ結果から
    * URLの一覧(ページング用)を生成する。
    * @param string $url
    * @param string $xpath
    *
    * @return array Orderの配列
    */
    public function getUrlListFromXPath($url, $xpathQuery) {
    
        $htmlText = $this->loadContentText($url);
        
        $domDoc = new \DOMDocument('1.0', 'UTF-8');
        @$domDoc->loadHtml($htmlText);
        $domXPath = new \DOMXPath($domDoc);
    
        $entries = $domXPath->query($xpathQuery);
        
        //抜き出した結果が属性値であると仮定してURLのリストを作る
        $urlList = array();
        $registeredUrl = array();
        foreach($entries as $entry) {
            $url = $this->getCompleteUrl($url, $entry->nodeValue);
            //重複するURLは除外しながらリストを作っていく。
            if(isset($registeredUrl[$url])) {
                continue;
            }
            $registeredUrl[$url] = 1;
            $urlList[] = $url;
        }
        return $urlList;
    }
    
    
    public function setCookie($cookie) {
        $this->cookie = $cookie;
    }
    
    
    /**
     * 指定されたファイルをロードして文字列で返す。
     * 現在の所HTTPのURLのみ対応。
     * @param string $url
     * @throws \Exception
     * @return string ファイルの内容の文字列
     */
    protected function loadContentText($url)
    {
        if(substr($url, 0, 2) == '//') {
            $url = 'http:' . $url;
        }
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    
        if($this->cookie != '') {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        }
        
        $response = curl_exec($ch);
        if(!$response) {
            throw new \Exception(curl_error($ch) . "\nURL:" . $url);
        }
        
        //ここでUTF-8に変換する
        $response = mb_convert_encoding($response, 'UTF-8', 'SJIS,EUC-JP,JIS,UTF-8,ASCII');
        
        //libxmlの文字コードの仕様対策。文字コードをmetaタグのContent-Typeで判定するので、EUC-JPのコンテンツや、HTML5のコンテンツ用に
        //UTF-8の文字コード指定付きのmetaタグを追加する。
        //libxml的には最初に見つけたContent-Typeで判定するようなので、charset=EUC-JPなどのmetaタグがすでにある場合でも辛うじてUTF-8として認識させられる。
        //本来は、オリジナルのmetaタグのcharsetをUTF-8に書き換えた方がよいと思われる。
        $response = preg_replace('|<head>|i', '<head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>', $response);
        
        //  $response = file_get_contents('response.txt');
    
        //コメントを全て取り除く。こうしないと
        //Domライブラリ的になぜか解析できない場合がある。
        //例：Javascriptコード中にHTMLエンティティが含まれていた場合。
        $this->stripComment($response);
    
        //   google+ 対策
        //   $response = str_replace('<g:', '<g', $response);
        //   facebook 対策
        //   $response = str_replace('<fb:', '<fb', $response);
    
        return $response;
    }
    
    
    /**
     * DOMからXPathを使ってドキュメントの一部を抜き出した結果を返す。
     * @param \DOMDocument $domDoc スクレイピングの対象となるコンテンツ
     * @param string $xPathQuery XPath文字列。
     * @return DOMNodeList スクレイピングした結果のDom要素一覧
      * 
     * TODO: DOMに依存しない値を受け取るように
     * TODO: DOMは毎回ツリー全体をメモリに展開するので、この処理を何度も呼び出す場合は効率が悪いため、効率的な方法を考える。
     */
    protected function extractContent($domDoc, $xPathQuery)
    {
        $domXPath = new \DOMXPath($domDoc);
    
        //gihyo
        //$entries = $domXPath->query("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/*");
        //$entries = $domXPath->query("//div[@class='readingContent01 autopagerize_page_element']/*");
    
        //symfony2-cookbook
        $entries = $domXPath->query($xPathQuery);
        return $entries;
    }
    
    
    /**
     * HTML中に含まれるコメントを除去する。
     * @param string $content
     * @return boolean
     */
    protected function stripComment(&$content) {
        
        //この処理は以下で置換できるかもしれな]い
        $pattern = "#(<!--(.*?)-->)#";
        $content = preg_replace($pattern, '', $content);
//         
//     
        // $beginPos = mb_strpos($content, '<!--');
        // if($beginPos === false) {
            // return false;
        // }
//     
        // $endPos = mb_strpos($content, '-->', $beginPos+3);
        // if($endPos === false) {
            // return false;
        // }
//     
        // $newContent = mb_substr($content, 0, $beginPos);
        // $newContent .= mb_strcut($content, $endPos+3);
        // $content = $newContent;
//     
        // return $this->stripComment($content);
    }
    
    
    /**
     * 結果格納用のドキュメントのHTMLタグ、HEADタグを、スクレイピング対象のドキュメント
     * を基に作成する。
     * 
     * @param \DomDocumrent $srcDoc スクレイピング対象のドキュメント
     */
    protected function initResultDocument(\DomDocument $srcDoc) {
        //TODO: DOCTYPE宣言の取得も行う
        //TODO: 元ドキュメントのHTML5のドキュメントはなぜか文字化けする。
        
        $baseElement = $this->resultDoc->createElement('html');
        //$this->resultDoc->importNode($baseElement, true);
        
        //htmlタグ属性の一覧を取得する。
        $attributes = $srcDoc->getElementsByTagName('html')->item(0)->attributes;
        foreach($attributes as $attrName=>$attr) {
            $baseElement->setAttribute($attrName, $attr->value);
        }
        $baseElement->setAttribute('xmlns', 'http://www.w3.org/1999/xhtml');
        $baseElement->setAttribute('xml:lang', 'ja');
        
        
        $headElement = $this->resultDoc->createElement('head');
        $baseElement->appendChild($headElement);
        
        //metaタグの一覧を取得する。
//         $metaTags = $srcDoc->getElementsByTagName('meta');
//         foreach($metaTags as $metaTag) {
//             $importedMetaTag = $this->resultDoc->importNode($metaTag, true);
//            DomDocument<meta>
//             $headElement->appendChild($importedMetaTag);
//         }

        $importedMetaTag = $this->resultDoc->createElement('meta');
        $importedMetaTag->setAttribute('http-equiv', 'Content-Type');
        $importedMetaTag->setAttribute('content', 'application/xhtml+xml; charset=UTF-8');
        $headElement->appendChild($importedMetaTag);
        
        //titleタグを取得する
        $titleTag = $srcDoc->getElementsByTagName('title')->item(0);
        if($titleTag) {
            $importedTitleTag = $this->resultDoc->importNode($titleTag, true);
            $headElement->appendChild($importedTitleTag);
        }
        
        $bodyElement = $this->resultDoc->createElement('body');
        $baseElement->appendChild($bodyElement);
        
        $this->resultDoc->appendChild($baseElement);
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
            $result = absUrl;
        }
        
        return $result;
    }
}

