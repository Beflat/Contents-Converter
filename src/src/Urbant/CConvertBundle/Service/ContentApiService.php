<?php

namespace Urbant\CConvertBundle\Service;

class ContentApiService {
    
    public function getContentXml($contents) {
        
        $doc = new \DomDocument('1.0', 'UTF-8');
        
        $contentListTag = $doc->createElement('ContentList');
        
        foreach($contents as $content) {
            $contentTag = $doc->createElement('Content');
            
            $contentTag->setAttribute('id', $content->getId());
            $contentTag->setAttribute('status', $content->getStatus());
            $contentTag->setAttribute('rule', $content->getRule()->getName());
            $contentTag->setAttribute('date', $content->getCreated()->format('Y-m-d H:i:s'));
            
            $title = $content->getTitle();
            if($title == '') {
                $title = ' ';
            }
            $contentTag->appendChild($doc->createTextNode($title));
            
            $contentListTag->appendChild($contentTag);
        }
        
        $doc->appendChild($contentListTag);
        return $doc->saveXml();
    }
    
}
