<?php


/**
 * ドキュメントのページを管理するクラス
 * @author hiroki
 *
 */
class DocPage
{
	
	/**
	 * セルの一覧
	 * @var Array(DocCell)
	 */
	protected $_cells;
	
	
	protected $_w;
	
	
	protected $_h;
	
	
	public function __construct(DocLayout $layout, $w, $h)
	{
	}
	
	public function addCell(DocCell $cell)
	{
		
	}
	
	
	public function getCells()
	{
	}
	
	
	public function getCell($idx)
	{
	}
	
	
	public function getCellCount()
	{
	}
	
	
	/**
	 * セルの横幅を返す。可変長の場合は計算した値を返す。
	 */
	public function getWidth()
	{
	}
	
	
	/**
	 * セルの縦幅を返す。可変長の場合は計算した値を返す。
	 */
	public function getHeight()
	{
	}
	
	
	protected function calculateWidth()
	{
	}
	
	
	protected function calculateHeight()
	{
	}
	
}
