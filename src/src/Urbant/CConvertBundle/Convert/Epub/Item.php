<?php

namespace Urbant\CConvertBundle\Convert\Epub;

class Item {
    
    protected $id;
    
    
    protected $href;
    
    
    protected $mediaType;
    
    
    public function __constructor() {
        
    }
    
    
    public function setId($id) {
        $this->id = $id;
    }
    
    
    public function getId() {
        return $this->id;
    }
    
    
    public function setHref($href) {
        $this->href = $href;
    }
    
    
    public function getHref() {
        return $this->href;
    }
    
    
    public function setData($id, $href, $mediaType) {
        $this->id = $id;
        $this->href = $href;
        $this->mediaType = $mediaType;
    }
    
    
    public function getData() {
        return array(
            'id' => $this->id,
            'href' => $this->href,
            'mediaType' => $this->mediaType
        );
    }
    
    /**
     * データの内容をXML化したものを返す。
     */
    public function getDataByXml() {
        
        $id = $this->id;
        $href = $this->href;
        $mediaType = $this->mediaType;
        
        return '<item id="' . htmlentities($id, ENT_QUOTES) . '" href="' . htmlentities($href, ENT_QUOTES) . '" media-type="' . htmlentities($mediaType, ENT_QUOTES) . '" />';
    }
    
    
    public function slugify($text) {
        $text = str_replace(' ', '_', $text);
        return $text;
    }
}