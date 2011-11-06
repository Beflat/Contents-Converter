<?php

namespace Urbant\CConvertBundle\Convert\Epub;

class ItemCollection {
    
    protected $items = array();
    
    
    public function __construct() {
    }
    
    
    public function add(Item $item) {
        $this->items[$item->getId()]->$item;
    }
    
    
    public function getItem($id) {
        if(!$this->isItemExists($id)) {
             return null;
        }
        
        return $this->items;
    }
    
    
    public function getItems() {
        return $this->items;
    }
    
    
    public function isItemExists($id) {
        return (isset($this->items[$id]));
    }
    
}