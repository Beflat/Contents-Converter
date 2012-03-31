<?php

namespace Urbant\CConvertBundle\Convert\Epub;


/**
 * 入力パラメータとして受け取ったデータの変換を行う。
 * 現時点ではテキストデータを受け取ってepub化するのみ
 */
class EpubConvertEngine {
    
    protected $outputPath;
    
    
    //epubのテンプレートが格納されたパス
    protected $workDirPath;
    
    
    /**
     * epubファイルの名称
     * @var string
     */
    protected $epubFileName;
    
    
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
     * タイトル
     * @var string
     */
    protected $title;
    
    /**
     * 本文のコンテンツを表すID
     * @var unknown_type
     */
    protected $mainContentId;
    
    
    const ZIP_COMMAND = '/usr/bin/zip';
    
    public function __construct() {
        $this->items = new ItemCollection();
        $this->uuid = $this->generateUuid();
    }
    
    
    public function execute() {
        
        //作業ディレクトリを走査する
        $resourceDirPath = $this->workDirPath . '/res';
        //(作業ディレクトリ/res等をリソース用のディレクトリに決めて、その中を走査するべき？)
//         if(!is_dir($resourceDirPath)) {
//            //TODO: 例外はどのように扱うべきか、他のライブラリ等を見て参考にする。
//             throw new \Exception('作業ディレクトリが存在しません。Path=' . $resourceDirPath);
//         }
        
        //各種ファイルを作業ディレクトリ上に出力
        
        //META-INF/container.xml
        $metaDir = $this->workDirPath . '/META-INF';
        if(!is_dir($metaDir)) {
            if(!mkdir($metaDir, 0777, true)) {
                throw new \Exception('ディレクトリの作成に失敗。Path=' . $metaDir);
            }
        }
        $containerXmlString = $this->getContainerXmlString();
        $containerXmlPath = $metaDir . '/container.xml';
        if(!file_put_contents($containerXmlPath, $containerXmlString)) {
            throw new \Exception('container.xmlの作成に失敗。Path=' . $containerXmlPath);
        }
        
        //package.opf
        $packageOpfString = $this->getPackageOpfString();
        $packageOpfPath = $this->workDirPath . '/package.opf';
        if(!file_put_contents($packageOpfPath, $packageOpfString)) {
            throw new \Exception('package.opfの作成に失敗。Path=' . $packageOpfPath);
        }
        
        //toc.ncx
        $tocString = $this->getTocString();
        $tocPath = $this->workDirPath . '/toc.ncx';
        if(!file_put_contents($tocPath, $tocString)) {
            throw new \Exception('toc.ncxの作成に失敗。Path=' . $tocPath);
        }
        
        //mimetype
        $mimeTypeString = $this->getMimeTypeString();
        $mimeTypePath = $this->workDirPath . '/mimetype';
        if(!file_put_contents($mimeTypePath, $mimeTypeString)) {
            throw new \Exception('mimetypeの作成に失敗。Path=' . $mimeTypePath);
        }
        
        
        //zip up. 
        $epubFilePath = $this->workDirPath . '/' . $this->epubFileName;
        $this->zipUpDir($epubFilePath, $this->workDirPath);
        
        //リネームして出力ディレクトリにコピー
        if(!@rename($epubFilePath, $this->outputPath.'/'.$this->epubFileName)) {
            throw new \Exception('epubファイルのリネームに失敗。：' . $epubFilePath
                . ' -> ' . $this->outputPath . '/');
        }
    }
    
    
    public function setOutputPath($path) {
        $this->outputPath = $path;
    }
    
    
    public function getOutputPath() {
        return $this->outputPath;
    }
    
    
    public function getWorkDirPath() {
        return $this->workDirPath;
    }
    
    
    public function setWorkDirPath($path) {
        $this->workDirPath = $path;
    }
    
    
    public function setMainContentId($id) {
        $this->mainContentId = $id;
    }
    
    
    public function setEpubFileName($name) {
        $this->epubFileName = $name;
    }
    
    public function addItem(Item $item) {
        $this->items->add($item);
    }
    
    
    public function setTitle($title) {
        $this->title = $title;
    }
    
    protected function getContainerXmlString() {
        
        return '<?xml version="1.0" encoding="UTF-8"?>
<container xmlns="urn:oasis:names:tc:opendocument:xmlns:container" version="1.0">
  <rootfiles>
    <rootfile full-path="package.opf" media-type="application/oebps-package+xml" />
  </rootfiles>
</container>';
    }
    
    
    protected function getTocString() {
        
        $uuid = $this->uuid;
        $title = htmlentities($this->title, ENT_QUOTES, 'UTF-8');
        $author = 'Epub Generate Engine';
        
        EntityEscape::replaceAllEntities($title);
        
        //TODO: $this->itemsを参照しない方法を考える
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
    <text>' . $title . '</text>
  </docTitle>
  <docAuthor>
    <text>' . htmlentities($author, ENT_QUOTES, 'UTF-8') . '</text>
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
        $uuid = $this->uuid;
        $title = htmlentities($this->title, ENT_QUOTES, 'UTF-8');
        EntityEscape::replaceAllEntities($title);
        
        $publisher = 'Epub Generate Engine';
        $author = 'Epub Generate Engine';
        
        $tocItem = new Item();
        $tocItem->setData('ncx', 'toc.ncx', 'application/x-dtbncx+xml');
        
        $itemString = "\n" . $tocItem->getDataByXml()."\n";
        foreach($this->items->getItems() as $item) {
            $itemString .= $item->getDataByXml() . "\n";
        }
        
        return '<?xml version="1.0" encoding="UTF-8"?>
<package version="2.0" xmlns="http://www.idpf.org/2007/opf" unique-identifier="BookId">
 <metadata xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:opf="http://www.idpf.org/2007/opf">
   <dc:title>' . $title . '</dc:title>
   <dc:creator opf:role="aut">' . htmlentities($author, ENT_QUOTES, 'UTF-8') . '</dc:creator>
   <dc:language>ja</dc:language>
   <dc:publisher>' . htmlentities($publisher, ENT_QUOTES, 'UTF-8') . '</dc:publisher>
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
    
    
    /**
     * mimetypeファイルの文字列を返す
     */
    public function getMimeTypeString() {
        return 'application/epub+zip';
    }
    
    
    protected function generateUuid() {
        return uniqid('cconvert_');
    }
    
    
    protected function zipUpDir($zipFileName, $targetDir) {
        
        if($zipFileName == '') {
            throw new \Exception('Zipファイル名が空白：' . $zipFileName);
        }
        
        //zipコマンドは、作業ディレクトリの場所が圧縮ファイルに影響するので、
        //CDで対象の場所まで移動する
        $currentDir = getcwd();
        
        if(!@chdir($targetDir)) {
            @chdir($currentDir);
            throw new \Exception('ディレクトリの移動に失敗：' . $targetDir);
        }
        
        $output = array();
        $retVal = '';
        $zipCommand = sprintf('%s -r %s * 2>&1', self::ZIP_COMMAND, escapeshellarg($zipFileName));
        if(!exec($zipCommand, $output, $retVal)) {
            throw new \Exception('Zipコマンドの実行に失敗：' . $zipCommand."\n"
                . "実行結果：" . var_export($output, true) . "\n"
                . "ステータスコード：" . $retVal . "\n");
        }
        
        @chdir($currentDir);
    }
}
