<?php



/**
 * セル内に格納される要素の基底クラス
 * @author hiroki
 */
class DocElement
{
	protected $_x;
	protected $_y;
	protected $_w;
	protected $_h;
	
	protected $_type;
	protected $_text;
	protected $_attributes;
	
	
	public function __construct($type, $text, $x=null, $y=null, $w=null, $h=null)
	{
		$this->_x = $x;
		$this->_y = $y;
		$this->_w = $w;
		$this->_h = $h;
		$this->_type = $type;
		$this->_text = $text;
		$this->_attributes = array();
	}
	
	public function getType()
	{ return $this->_type; }
	
	public function getText()
	{ return $this->_text; }
	public function setText($text)
	{ $this->_text = $text; }
		
	
	public function hasAttribute($key)
	{ return isset($this->_attributes[$key]); }
	public function getAttribute($key, $default=null)
	{ return ($this->hasAttribute($key)) ? $this->_attributes[$key] : $default; }
	public function setAttribute($key, $value)
	{ $this->_attributes[$key] = $value; }
	
	
	public function getAttributes()
	{ return $this->_attributes; }
	public function setAttributes($attributes)
	{ $this->_attributes = array_merge($this->_attributes, $attributes); }
	
	
	public function getX()
	{ return $this->_x; }
	public function setX($x)
	{ $this->_x = $x; }
	
	
	public function getY()
	{ return $this->_y; }
	public function setY($y)
	{ $this->_y = $y; }
	
	
	public function getWidth()
	{ return $this->_w; }
	public function setWidth($w)
	{ $this->_w = $w; }
	
	
	public function getHeight()
	{ return $this->_h; }
	public function setHeight($h)
	{ $this->_h = $h; }
	
	
	protected function calculateWidth()
	{
	}
	
	
	protected function calculateHeight()
	{
	}
	
}
