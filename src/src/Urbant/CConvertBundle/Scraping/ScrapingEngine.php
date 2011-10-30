<?php

namespace Urbant\CConvertBundle\Scraping;


class ScrapingEngine {
    
    protected $orders;
    
    
    protected $outputDir;
    
    
    //イベント識別子
    const ON_SCRAPING_DONE = 'on_scraping_done';
    
    public function __construct() {
        
        $this->orders = array();
        
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
        
        foreach($this->orders as &$order) {
            
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
            $domDoc = new \DOMDocument();
            @$domDoc->loadHtml($htmlText);
            
            $scrapedNodes = $this->extractContent($domDoc, $xPathString);
            
            $order->onEvent(self::ON_SCRAPING_DONE, array('node_list'=>$scrapedNodes));
            
            $content = '';
            $document = new \DOMDocument('1.0','UTF-8');
            $baseElement = $document->createElement('html');
            $document->importNode($baseElement, true);
            foreach($scrapedNodes as $entry) {
                $document->appendChild($document->importNode($entry, true));
            }
            
            $scrapedHtmlText = $document->saveHtml();
            $order->setResult($scrapedHtmlText);
            $order->setStatus(Order::STATE_SUCCEED);
        }
        
        
        //TODO:全オーダーが正常に完了した事を確認する。
        //TODO:ファイルへの保存を行うか、Engineの利用側でOrderの一覧にする手段を提供する
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
        
        //TODO: CURLではなくSymfonyの機能を使用する。
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
        $response = curl_exec($ch);
        if(!$response) {
            throw new \Exception(curl_error($ch) . "\nURL:" . $url);
        }
    
        //  $response = file_get_contents('response.txt');
    
        //コメントを全て取り除く。こうしないと
        //Domライブラリ的になぜか解析できない場合がある。
        //例：Javascriptコード中にHTMLエンティティが含まれていた場合。
        $this->stripComment($response);
    
        //   google+ 対策
        //   $response = str_replace('<g:', '<g', $response);
        //   facebook 対策
        //   $response = str_replace('<fb:', '<fb', $response);
    
        //   $fp = fopen('response.txt','w');
        //   fputs($fp, $response);
        //   fclose($fp);
    
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
        //TODO: 設定情報をOrderから取得する。
        $entries = $domXPath->query($xPathQuery);
        return $entries;
    }
    
    
    /**
     * HTML中に含まれるコメントを除去する。
     * @param string $content
     * @return boolean
     */
    protected function stripComment(&$content) {
    
        $beginPos = mb_strpos($content, '<!--');
        if($beginPos === false) {
            return false;
        }
    
        $endPos = mb_strpos($content, '-->', $beginPos+3);
        if($endPos === false) {
            return false;
        }
    
        $newContent = mb_substr($content, 0, $beginPos);
        $newContent .= mb_strcut($content, $endPos+3);
        $content = $newContent;
    
        return $this->stripComment($content);
    }
    
    
}

