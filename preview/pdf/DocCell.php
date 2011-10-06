<?php


/**
 * 要素のグループを管理するオブジェクト。要素の枠。
 * @author hiroki
 */
class DocCell
{
	
	protected $_elements;
	
	protected $_w;
	
	protected $_h;
	
	
	public function __construct($x=-1, $y=-1, $w=-1, $h=-1)
	{
	}
	
	
	public function getElements()
	{
	}
	
	
	public function getElement($index)
	{
	}
	
	
	public function addElement($element)
	{
	}
	
	
	public function getElementCount()
	{
	}
	
	
	public function getX()
	{
	}
	
	
	public function getY()
	{
	}
	
	
	public function getWidth()
	{
	}
	
	
	public function getHeight()
	{
	}
	
	
	/**
	 * 全ての要素の最下部の座標を返す。
	 */
	public function getElementBottom()
	{
	}
	
	
	protected function calculateWidth()
	{
	}
	
	
	protected function calculateHeight()
	{
	}
}
