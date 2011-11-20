<?php

namespace Urbant\CConvertBundle\Service;

use Doctrine\ORM\EntityManager;

use Urbant\CConvertBundle\Entity\ConvertRequest;

/**
 * リクエストログに関する共通処理を定義するクラス。
 * 
 * 共通処理野中で、Entity単体では完結しない、
 * 外部のオブジェクトとの連携が発生する処理を定義します。
 * (RepositoryやEntityManager、設定情報など、Entityから直接アクセスできない情報)
 */

class ConvertRequestService {
    
    /**
     * @var Doctrine\ORM\EntityManager
     */
    protected $entityManager;
    
    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }
    
    /**
     * リクエストログの保存を行う。
     * @param ConvertRequest $request
     * 
     * この処理野中でflushまでは行わないため、
     * flush()は呼び出し元で実行する必要があります。
     */
    public function saveRequest(ConvertRequest $request) {
        
        if(is_null($request->getRule())) {
            //URLにマッチするルールを検索する。
            $ruleRepo = $this->entityManager->getRepository('UrbantCConvertBundle:Rule');
            
            $matchedRule = $ruleRepo->findRuleForUrl($request->getUrl());
            if(!is_null($matchedRule)) {
                $request->setRule($matchedRule);
            } else {
                //URLにマッチするルールは存在しなかった。
                $request->appendLog('URLにマッチするルールが存在しませんでした。');
                $request->setStatus($request::STATE_FAILED);
            }
        }
        
        if(!$request->getId()) {
            $this->entityManager->persist($request);
        }
    }
    
}