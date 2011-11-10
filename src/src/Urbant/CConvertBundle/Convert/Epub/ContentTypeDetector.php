<?php

namespace Urbant\CConvertBundle\Convert\Epub;


/**
 * 指定されたファイル名やその内容から、コンテンツタイプを判定して返す。
 */
class ContentTypeDetector {
    
    
    /**
     * ファイル名からコンテンツタイプを解析する
     * @param string $fileName ファイル名
     * @return string epubで使用できるContent-Type
     */
    public function detectFromFileName($fileName) {
        $explode = explode('.', $fileName);
        $extension = array_pop($explode);
        return $this->detectFromExt($extension);
    }
    
    
    /**
     * ファイルの拡張子からコンテンツタイプを解析する
     * @param string $extension ファイル名
     * @return string epubで使用できるContent-Type
     */
    public function detectFromExt($extension) {
        
        switch(strtolower($extension)) {
            case 'gif':
                return 'image/gif';
                
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            
            case 'png':
                return 'image/png';
            
            case 'css':
                return 'text/css';
                
            case 'xhtml':
                //epub固有
                return 'application/xhtml+xml';
                
            case 'ncx':
                //epub固有
                return 'text/xml';
        }
        
        return false;
    }
    
    
    /**
     * 指定されたファイルを開き、マジックナンバー(File Signature)を調べて
     * Content-Typeを決定する。
     * @param unknown_type $filePath
     * @return string Content-Type文字列
     */
    public function detectFromContent($filePath) {
        
        //TODO: jpegなら7バイト目からがJFIF
        //TODO: GIFなら先頭3バイトがGIF
        //TODO: PNGなら2バイト目からがPNG
        //TODO: HTML/XHTMLは先頭500バイト程度以内に<html>
        //TODO: CSSは非HTMLで、CSS的キーワードがあること？
        
    }
}