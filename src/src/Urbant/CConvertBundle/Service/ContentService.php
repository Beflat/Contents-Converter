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
    
    
    /**
     * コンテンツのディレクトリのパス情報を返す
     * @param Content $content コンテンツ
     * @return string パス文字列
     */
    public function getContentDirPath(Content $content) {
        $basePath = $this->container->getParameter('urbant_cconvert.content_dir_path');
        return sprintf('%s/%010d', $basePath, $content->getById());
    }
    
    
    /**
    * コンテンツのファイル名を返す
    * @param Content $content コンテンツ
    * @return string ファイル名
    */
    public function getContentFileName(Content $content) {
        $fileName = sprintf('cc_%06d.epub', $content->getId());
        return $fileName;
    }
    
    
    /**
     * コンテンツのファイルのパス情報を返す
     * @param Content $content コンテンツ
     * @return string パス文字列
     */
    public function getContentFilePath(Content $content) {
        
        $baseDirPath = $this->getContentDirPath($content);
        return sprintf('%s/%s', $baseDirPath, $this->getContentFileName($content));
    }
    
    
}