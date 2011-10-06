<?php

//phpinfo();die;

require_once '../common.php';

require_once 'Zend/Pdf.php';

setlocale(LC_ALL, 'ja_JP');

$pdf = new Zend_Pdf();


//B5サイズのポイント
$w = (int)(7.16535433 * 72);
$h = (int)(10.1181102 * 72);

$pdfPage = new Zend_Pdf_Page($w, $h);
$pdf->pages[] = $pdfPage;

//$font = Zend_Pdf_Font::fontWithPath('TakaoGothic.ttf');
//$font = Zend_Pdf_Font::fontWithPath('TakaoPMincho.ttf');
$font = Zend_Pdf_Font::fontWithPath('ipag.ttf', Zend_Pdf_Font::EMBED_DONT_EMBED);


$pdfPage->setFont($font, 36);

//$gryphTests = array(getCharCode('H'),getCharCode('I'),getCharCode('M'),getCharCode('あ'),getCharCode('Ｉ'));
$char = 'H';
echo $char . ' Width=' . $font->widthForGlyph(getCharCode($char)) . "<br />\n";



$pdfPage->drawText('Hello world!', 0, 0);

$pdfPage->drawText('日本語テスト', 10, 40, 'UTF-8');
$pdfPage->drawText('日本語テストＩＩ。！折り返しはどうなるのか', 20, 70, 'UTF-8');

$pdf->save('./data/test1.pdf');


function getCharCode($c)
{
	$result = 0;
	$len = strlen($c);
	for($i=0;$i<$len;$i++)
	{
		$result <<= 8;
		$result += ord($c[$i]);
	}
	
	return $result;
}

echo 'DONE!';
