<?php

namespace Urbant\CConvertBundle\Convert\Epub;

/**
 * XMLなどの中に含まれるHTMLエンティティを実態参照に変換するユーティリティ
 */
class EntityEscape {
    
    private static $entityMap = null;
    
    
    public static function convert($target) {
        
        if(is_null(self::$entityMap)) {
            self::loadEntityMap();
        }
        
        $entityName = str_replace(array('&', ';'), '', $target);
        
        if(!isset(self::$entityMap[$entityName])) {
            //マッチしない物は警告を挙げてそのまま返す。
            trigger_error('Unknown entity: ' . $target, E_USER_WARNING);
            return $target;
        }
        
        return self::$entityMap[$entityName];
    }
    
    
    /**
     * 対象のテキスト中にある全てのHTMLエンティティを一括で置換する。
     * @param string $targetDocument HTMLエンティティを除きたい変換対象の文字列全体(XMLなど)
     */
    public static function replaceAllEntities(&$targetDocument) {
        
        $matches = array();
        $targetDocument = preg_replace('/(&(.*?);)/ie', 'self::convert("\1")', $targetDocument);
        
    }
    
    private static function loadEntityMap() {
        
        $fp = fopen(dirname(__FILE__).'/entity_map.csv', 'r');
        if(!$fp) {
            trigger_error(var_export(error_get_last(), true), E_USER_WARNING);
            return;
        }
        
        while(!feof($fp)) {
            $csvLine = fgetCsv($fp, 1024);
            if(!is_array($csvLine) || count($csvLine) == 1) {
                continue;
            }
            self::$entityMap[ trim($csvLine[0]) ] = trim($csvLine[1]);
        }
    }
    
}