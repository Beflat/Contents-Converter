<?php 

require_once '../common.php';

require_once 'Zend/Http/Client.php';


/* SourceForge.jp
//$url = 'http://sourceforge.jp/magazine/09/03/16/0831212';
$url = 'http://sourceforge.jp/magazine/09/05/15/0930226';
$responseBody = getContentFromUrl($url);
$domDoc = new DOMDocument();
$domDoc->loadHTML($responseBody);
$result = getContentFromDom($domDoc);

$urls = findPagingUrls($domDoc);
foreach($urls as $url)
{
	$responseBody = getContentFromUrl($url);
	if($responseBody == '') {
		continue;
	}
	
	$result .= "====" . $url . "===================================================================<br />\n";
	
	$domDoc = new DOMDocument();
	$domDoc->loadHTML($responseBody);
	$result .= getContentFromDom($domDoc);
}
echo $result;
*/

//gihyo.jp
$url = 'http://gihyo.jp/dev/serial/01/search-engine/0002';
$responseBody = getContentFromUrl($url);
$domDoc = new DOMDocument();
$domDoc->loadHTML($responseBody);
$nodes = getNodesFromDom($domDoc);

$newDoc = new DOMDocument('1.0','UTF-8');
foreach($nodes as $node)
{
	$newDoc->appendChild($newDoc->importNode($node, true));
}
echo $newDoc->saveHTML();




function getContentFromUrl($url)
{
	if(substr($url, 0, 2) == '//') {
		$url = 'http:' . $url;
	}
	
	
	$client = new Zend_Http_Client($url, array(
	    'maxredirects' => 10,
	    'timeout'      => 30));
	
	$response = $client->request();
	
	if($response->isError()) {
		$error = array('status'=>$response->getStatus(), 'message'=>$response->getMessage());
		trigger_error(var_export($error, true));
		return var_export($error, true);
	}
	
	return $response->getBody();
}


function getContentFromDom($domDoc)
{
	$domXPath = new DOMXPath($domDoc);
	
	//sourceforge.jp
	$entries = $domXPath->query("//div[@id='article-body']/div[@class='body']/*");
	
	$content = '';
	foreach($entries as $entry)
	{
		$content .= $document->saveHtml($entry);
	}
	
	return $content;
}

function getNodesFromDom($domDoc)
{
	$domXPath = new DOMXPath($domDoc);
	
	//gihyo.jp
	$entries = $domXPath->query("//div[@id='article']/div[@class='readingContent01']/*");
	
	return $entries;
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
	
	$entries = $domXPath->query("//span[@class='pagemenu']/a");
	
	$content = '';
	$urls = array();
	foreach($entries as $entry)
	{
/*
		var_export(array(
			'name' => $entry->nodeName,
			'value' => $entry->nodeValue,
			'childNodes' => $entry->childNodes->length
		));
		echo "<br />\n====================================================================================<br />\n";
*/		
		
		$href =trim($entry->getAttribute('href'));
		if($href == '') {
			continue;
		}
		$urls[] = $href;
	}
	
	return $urls;
}


