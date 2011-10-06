<?php


/**
 * ドキュメントの現在の編集位置、ページを管理するオブジェクト
 * @author hiroki
 *
 */
class DocContext
{
	/**
	 * レイアウト
	 * @var DocLayout
	 */
	protected $_layout;
	
	
	protected $_y;
	
	
	protected $_currentPage;
	
	
	protected $_currentCell;
	
	
	public function __construct(DocLayout $layout)
	{
	}
	
	
	/**
	 * 現在のページの最下部にセルを追加する。
	 * @param DocCell $cell
	 * 
	 * セルが入りきらない場合は次ページへ追加する。
	 * 1ページを超える縦幅のセルは現時点ではサポートできない
	 */
	public function appendCell(DocCell $cell)
	{
	}
	
	
	/**
	 * 現在のセルの最下部に要素を追加する。
	 * @param DocElement $element
	 * 
	 * セルの縦幅に上限がある場合でもそのまま追加する。
	 */
	public function appendElement(DocElement $element)
	{
	}
	
	
	public function getCurrentPage()
	{
	}
	
	
	public function getCurrentCell()
	{
	}
	
	
	public function canInsertCurrentPage(DocElement $element)
	{
	}
	
}
