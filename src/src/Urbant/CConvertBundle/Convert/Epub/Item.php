<?php

namespace Urbant\CConvertBundle\Convert\Epub;

class Item {
    
    protected $id;
    
    
    protected $href;
    
    
    protected $mediaType;
    
    
    public function __constructor() {
        
    }
    
    
    public function setId($id) {
    }
    
    
    public function getId() {
    }
    
    
    public function setHref($href) {
    }
    
    
    public function getHref() {
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
        
        $id = '';
        $style = '';
        $mediaType = '';
        
        return '<item id="' . htmlentities($id, ENT_QUOTES) . '" href="' . htmlentities($href, ENT_QUOTES) . '" media-type="' . htmlentities($mediaType, ENT_QUOTES) . '" />';
    }
}