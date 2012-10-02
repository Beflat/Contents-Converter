<?php

namespace Urbant\CConvertBundle\Service;

use Doctrine\ORM\EntityManager;
use Urbant\CConvertBundle\Scraping\HtmlLoader;
use Urbant\CConvertBundle\Entity\ConvertRequest;
use Beflat\HttpClient\HttpClientCurl;
use Beflat\HttpClient\UrlResolver;
use Beflat\HttpClient\Exceptions\HttpException;

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
        
        $this->setHtmlTitle($request);
        
        //URLの展開を試みる。
        //リダイレクトがなくなるまでHTTPリクエストを繰り返す。
        //リダイレクトの指示がなくなった時点でのURLでリクエスト自体のURLを置き換える。
        try {
            $httpClient = new HttpClientCurl();
            $urlResolver = new UrlResolver($httpClient);
            $urlResolver->init($request->getUrl());
            $resolvedUrl = $urlResolver->resolve();
            $request->setUrl($resolvedUrl);
        } catch(HttpException $httpClientException) {
            $request->appendLog('URLの自動展開に失敗しました。Status Code=' . $httpClientException->getMessage());
        }
        
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
    
    
    private function setHtmlTitle($request) {
        
        $htmlLoader = new HtmlLoader($request->getUrl());
        if(!$htmlLoader->loadHtml()) {
            $request->setTitle('Unknown');
            $request->appendLog($htmlLoader->getError());
            return;
        }
        
        $request->setTitle($htmlLoader->getTitle());
    }
    
    
    private function resolveUrl() {
        
    }
    
}