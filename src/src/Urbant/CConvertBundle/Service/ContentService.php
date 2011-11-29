<?php

namespace Urbant\CConvertBundle\Service;

use Symfony\Component\DependencyInjection\ContainerAware;
use Doctrine\ORM\EntityManager;
use Urbant\CConvertBundle\Entity\Content;


/**
 * コンテンツに関する共通処理を定義するクラス
 * 
 * 共通処理野中で、Entity単体では完結しない、
 * 外部のオブジェクトとの連携が発生する処理を定義します。
 * (RepositoryやEntityManager、設定情報など、Entityから直接アクセスできない情報)
 */
class ContentService extends ContainerAware {
    
    protected $entityManager;
    
    public function __construct(EntityManager $em) {
        
        $this->entityManager = $em;
        
    }
    
    
    public function removeContent(Content $content) {
        
        
    }
    
}