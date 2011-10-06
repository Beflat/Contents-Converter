<?php

ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', './error.log');

//gihyo.jp
$url = 'http://gihyo.jp/admin/serial/01/mixi_sd/0001';
$responseBody = getContentFromUrl($url);
$responseBody = mb_convert_encoding($responseBody, 'HTML-ENTITIES', 'ASCII, JIS, UTF-8, EUC-JP, SJIS');
$domDoc = new DOMDocument('1.0', 'UTF-8');
@$domDoc->loadHTML($responseBody);

$urls = findPagingUrls($domDoc);

$result = '';
foreach($urls as $url)
{
  $responseBody = getContentFromUrl($url);
  if($responseBody == '') {
    continue;
  }

  $result .= "====" . $url . "===================================================================<br />\n";

  $domDoc = new DOMDocument();
  @$domDoc->loadHTML($responseBody);
  $result .= getContentFromDom($domDoc);

}

file_put_contents('result.html', $result);



function getContentFromUrl($url)
{
  if(substr($url, 0, 2) == '//') {
    $url = 'http:' . $url;
  }

  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);
  if(!$response) {
    throw new Exception(curl_error($ch));
  }

  //  $response = file_get_contents('response.txt');

  //コメントを全て取り除く
  stripComment($response);

  //   google+ 対策
  //   $response = str_replace('<g:', '<g', $response);
  //   facebook 対策
  //   $response = str_replace('<fb:', '<fb', $response);

  //   $fp = fopen('response.txt','w');
  //   fputs($fp, $response);
  //   fclose($fp);

  return $response;
}


function getContentFromDom($domDoc)
{
  $domXPath = new DOMXPath($domDoc);

  $entries = $domXPath->query("//div[contains(concat(' ',normalize-space(@class),' '), 'readingContent01')]/*");
  //$entries = $domXPath->query("//div[@class='readingContent01 autopagerize_page_element']/*");


  $content = '';
  $document = new DomDocument('1.0','UTF-8');
  $baseElement = $document->createElement('html');
  $document->importNode($baseElement, true);
  foreach($entries as $entry) {

    scanResource($entry);

    $document->appendChild($document->importNode($entry, true));
  }
  $content = $document->saveHtml();

  return $content;
}



function getTextFromNode($node, $prefix="")
{
  if($node->nodeType == XML_TEXT_NODE) {
    if(strlen(trim($node->textContent)) == 0) {
      return $prefix;
    }
    return $prefix . $node->textContent;
  }

  $content = '';
  /*
   var_export(array(
  'name' => $node->nodeName,
  'value' => $node->nodeValue,
  'childNodes' => $node->childNodes->length
  ));
  echo "<br />\n====================================================================================<br />\n";
  */
  if(!isset($node->childNodes->length)) {
    return $prefix . $content;
  }
  foreach($node->childNodes as $child)
  {
    $content = getTextFromNode($child, $content);
  }

  return $prefix . $content;
}


function findPagingUrls($domDoc)
{
  $domXPath = new DOMXPath($domDoc);

  $entries = $domXPath->query("//div[@class='pageSwitch01']/ul/li/a");

  $content = '';
  $urls = array();
  foreach($entries as $entry)
  {
    $href =trim($entry->getAttribute('href'));
    if($href == '') {
      continue;
    }
    $urls[] = $href;
  }

  return $urls;
}


function stripComment(&$content) {

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

  return stripComment($content);
}


function scanResource(DOMElement $element) {
  static $resNo = 0;
  if($element->hasChildNodes()) {
    foreach($element->childNodes as $child) {
      if($child->nodeType != XML_ELEMENT_NODE) {
        continue;
      }
      scanResource($child);
    }
  }
  if($element->nodeName == 'img') {

    $imgSource = $element->getAttribute('src');
    //TODO:拡張子はコンテンツタイプで決定する
    $pathParts = explode('.', $imgSource);
    $ext = array_pop($pathParts);
    $baseDir = 'OEBPS/imgs';
    $destImgPath = sprintf('%s/%s.%s', $baseDir, $resNo, $ext);

    downloadResource($imgSource, $destImgPath);
    $element->setAttribute('src', $destImgPath);

    $resNo++;

    echo sprintf("Convert img from '%s' to '%s'\n", $imgSource, $destImgPath);
  }
}

function downloadResource($sourceURL, $savePath) {

  $ch = curl_init($sourceURL);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

  $response = curl_exec($ch);
  if(!$response) {
    throw new Exception(curl_error($ch));
  }

  if(!is_dir(dirname($savePath))) {
    mkdir($savePath, 0777, true);
  }
  
  file_put_contents($savePath, $response);
}

