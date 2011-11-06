<?php

namespace Urbant\CConvertBundle\Convert\Epub;


/**
 * 入力パラメータとして受け取ったデータの変換を行う。
 * 現時点ではテキストデータを受け取ってepub化するのみ
 */
class EpubConvertEngine {
    
    protected $outputPath;
    
    
    //-- epub固有 --
    
    //epubのテンプレートが格納されたパス
    protected $workDirPath;
    
    
    /**
     * 本文のXHTMLやCSS、画像のファイル名/Content-Typeなどの情報
     * @var Urbant\CConvertBundle\Convert\Epub\ItemCollection
     */
    protected $items;
    
    
    /**
     * epub文書のUUID
     * @var unknown_type
     */
    protected $uuid;
    
    
    /**
     * 本文のコンテンツを表すID
     * @var unknown_type
     */
    protected $mainContentId;
    
    public function __construct() {
        $this->items = new ItemCollection();
    }
    
    
    public function execute() {
        
        //TODO:作業ディレクトリを走査する
        //(作業ディレクトリ/res等をリソース用のディレクトリに決めて、その中を走査するべき？)
        
            //TODO:コンテンツタイプを取得する
            //Itemを生成、コレクションに追加

        //各種ファイルを作業ディレクトリ上に出力
        //zip up. 
        //リネームして出力ディレクトリにコピー
    }
    
    
    public function setOutputPath() {
    }
    
    
    public function getOutputPath() {
    }
    
    
    public function getWorkDirPath() {
    }
    
    
    public function setWorkDirPath() {
    }
    
    
    public function setMainContentId($id) {
        $this->mainContentId = $id;
    }
    
    
    public function addItem(Item $item) {
        $this->items->add($item);
    }
    
    protected function getContainerXmlString() {
        
        return '<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="urn:oasis:names:tc:opendocument:xmlns:container" version="1.0">
  <rootfiles>
    <rootfile full-path="OEBPS/package.opf" media-type="application/oebps-package+xml" />
  </rootfiles>
</container>';
    }
    
    
    protected function getTocString() {
        
        $uuid = '';
        $title = '';
        $author = 'Epub Generate Engine';
        
        $contentXhtmlName = '';
        $mainContent = $this->items->getItem($this->mainContentId);
        if(!is_null($mainContent)) {
            $contentXhtmlName = $mainContent->getHref();
        }
        
        return '<?xml version="1.0" encoding="UTF-8"?>
<ncx xmlns="http://www.daisy.org/z3986/2005/ncx/" version="2005-1">
  <head>
    <meta name="dtb:uid" content="' . $uuid . '"/>
    <meta name="dtb:depth" content="1"/>
    <meta name="dtb:totalPageCount" content="0"/>
    <meta name="dtb:maxPageNumber" content="0"/>
  </head>
  <docTitle>
    <text>' . htmlentities($title, ENT_QUOTES) . '</text>
  </docTitle>
  <docAuthor>
    <text>' . htmlentities($author, ENT_QUOTES) . '</text>
  </docAuthor>
  <navMap>
    <navPoint id="page" playOrder="1">
      <navLabel>
        <text>Index page</text>
      </navLabel>
      <content src="' . $contentXhtmlName . '"/>
    </navPoint>
  </navMap>
</ncx>';
    }
    
    
    protected function getPackageOpfString() {
        $uuid = '';
        $title = '';
        $author = 'Epub Generate Engine';
        
        $itemsString = array();
        foreach($this->items->getItems() as $item) {
            $itemString .= $item->getDataAsXml() . "\n";
        }
        
        return '<?xml version="1.0" encoding="UTF-8"?>
<package version="2.0" xmlns="http://www.idpf.org/2007/opf" unique-identifier="BookId">
 <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">
   <dc:title>' . htmlentities($title, ENT_QUOTES) . '</dc:title>
   <dc:creator opf:role="aut">' . htmlentities($author, ENT_QUOTES) . '</dc:creator>
   <dc:language>ja</dc:language>
   <dc:publisher>' . htmlentities($publisher, ENT_QUOTES) . '</dc:publisher>
   <dc:identifier id="BookId">urn:uuid:' . $uuid . '</dc:identifier>
 </metadata>
 <manifest>' . $itemString . ' </manifest>
 <spine toc="ncx">
  <itemref idref="' . $this->mainContentId . '" />
 </spine>
</package>';
        
//     <item id="ncx" href="toc.ncx" media-type="text/xml" />
//     <item id="style" href="style.css" media-type="text/css" />
//     <item id="page" href="' . $this->contentXhtmlName . '" media-type="application/xhtml+xml" />
    }
    
    
}
