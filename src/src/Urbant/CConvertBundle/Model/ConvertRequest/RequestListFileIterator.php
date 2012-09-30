<?php

namespace Urbant\CConvertBundle\Model\ConvertRequest;


use Urbant\CConvertBundle\Entity\ConvertRequest;

class RequestListFileIterator implements \Iterator {
    
    private $path;
    
    private $fp;

    private $current;
    
    private $key;
    
    public function __construct($path) {
        $this->path = $path;
        $this->key = 0;
        $this->current = '';
        $this->fp = null;
    }
    
    // --- Iterator interface  ---
    
    /**
     * @return Urbant\CConvertBundle\Entity\ConvertRequest リクエスト
     */
    public function current() {
        $this->openFile();
        $request = new ConvertRequest();
        $request->setUrl(trim($this->current));
        return $request;
    }
    
    /**
     * @return int 行番号
     */
    public function key() {
        $this->openFile();
        return $this->key;
    }
    
    
    public function next() {
        $this->openFile();
        $this->current = fgets($this->fp, 4096);
        $this->key++;
        return true;
    }
    
    public function valid() {
        $this->openFile();
        return $this->current !== false;
    }
    
    public function rewind() {
        if($this->fp) {
            fclose($this->fp);
        }
        $this->fp = null;
        $this->openFile();
    }
    
    
    private function openFile() {
        if($this->fp != null) {
            return;
        }
        $this->fp = @fopen($this->path, 'r');
        if(!$this->fp) {
            throw new \RuntimeException('ファイルオープンに失敗: ' . $this->path);
        }
        
        //1行目を読み込む
        $this->next();
    }
}

