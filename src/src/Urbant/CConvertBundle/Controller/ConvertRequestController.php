<?php

namespace Urbant\CConvertBundle\Controller;

use Pagerfanta\Pagerfanta;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Urbant\CConvertBundle\Form\ConvertRequestSearchType;
use Urbant\CConvertBundle\Entity\ConvertRequest;
use Urbant\CConvertBundle\Form\ConvertRequestType;
use Pagerfanta\Adapter\DoctrineORMAdapter;


class ConvertRequestController extends BaseAdminController
{

    
    protected $pageCatId = 'request';
    
    /**
     * 一覧表示
     * @param unknown_type $page
     */
    public function listAction()
    {
        $this->pageId = 'list';
        $reqRepo = $this->getRepository('UrbantCConvertBundle:ConvertRequest');
        
        $form = $this->createForm(new ConvertRequestSearchType());
        $request = $this->getRequest();
        $form->bindRequest($request);
        
        $searchConditions = $form->getData();
        
        $qb = $reqRepo->getQueryBuilderForSearch($searchConditions);
        
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        
        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage($request->attributes->get('page', 1));
        $requests = $pagerfanta->getCurrentPageResults();

        $vars = array(
            'requests' => $requests, 
            'search_form' => $form->createView(),
            'pager' => $pagerfanta
        );
        
        return $this->render('UrbantCConvertBundle:ConvertRequest:list.html.twig', $vars);
    }


    /**
     * 削除等の処理の一括実行
     */
    public function batchAction($page) {

        //TODO: このままだとページング情報等を引き回せない。
        
        $this->pageId = 'list';
        
        $request = $this->getRequest();
        $repository = $this->getRepository('UrbantCConvertBundle:ConvertRequest');
        $type = $request->request->get('type');

        switch($type) {
            case 'd':
                $repository->deleteRequestForIds($request->get('ids'));
                $this->get('session')->setFlash('convert_request_message', '選択したデータを削除しました。');
                break;
            default:
                $this->get('session')->setFlash('convert_request_message', '無効な区分です：' . $type);
        }

        return $this->listAction();
    }

    /**
     * 新規登録画面の表示
     */
    public function addAction() {

        $this->pageId = 'add';
        
        $request = new ConvertRequest();
        $form = $this->createForm(new ConvertRequestType(), $request);
        $vars = array(
            'form' => $form->createView(),
        );
        return $this->render('UrbantCConvertBundle:ConvertRequest:add.html.twig', $vars);
    }


    /**
     * 新規登録書利の実行
     */
    public function createAction() {

        $this->pageId = 'add';
        
        $convertRequest = new ConvertRequest();
        $form = $this->createForm(new ConvertRequestType(), $convertRequest);

        $request = $this->getRequest();
        $form->bindRequest($request);
        if($form->isValid()) {

            $em = $this->getEntityManager();
            $convertRequestService = $this->get('urbant_cconvert.convert_request_service');
            
            //変換ルールの自動判定などを行ってから保存
            $convertRequestService->saveRequest($convertRequest);
            
            $em->flush();

            $this->get('session')->setFlash('request_add_message', '登録しました。');
            return $this->redirect($this->generateUrl('UrbantCConvertBundle_request_add'));
        }

        $vars = array(
            'form' => $form->createView(),
        );
        return $this->render('UrbantCConvertBundle:ConvertRequest:add.html.twig', $vars);
    }


    /**
     * 編集画面の表示
     */
    public function editAction($id) {
        
        $repository = $this->getRepository('UrbantCConvertBundle:ConvertRequest');
        
        $convertRequest = $repository->find($id);
        if(!$convertRequest) {
            throw new $this->createNotFoundException('ID:' . $id . 'のリクエストは存在しません。');
        }
        
        $form = $this->createForm(new ConvertRequestType(), $convertRequest);
        
        $vars = array(
            'requestId' => $id,
            'form' => $form->createView(),
        );
        return $this->render('UrbantCConvertBundle:ConvertRequest:edit.html.twig', $vars);
    }
    
    
    public function updateAction($id) {
        
        $em = $this->getEntityManager();
        $coevnrtRequest = $this->getRepository('UrbantCConvertBundle:ConvertRequest')->find($id);
        if(!$coevnrtRequest) {
            throw new $this->createNotFoundException('ID:' . $id . 'のリクエストは存在しません。');
         }
        $form = $this->createForm(new ConvertRequestType(), $coevnrtRequest);
        
        $request = $this->getRequest();
        $form->bindRequest($request);
        
        if($form->isValid()) {
            $convertRequestService = $this->get('urbant_cconvert.convert_request_service');
            
            //変換ルールの自動判定などを行ってから保存
            $convertRequestService->saveRequest($coevnrtRequest);
            
            $em->flush();
            
            $this->get('session')->setFlash('request_edit_message', 'サイト情報を更新しました。');
            $this->redirect($this->generateUrl('UrbantCConvertBundle_request_edit', array('id' => $id), true));
        }
        
        $vars = array(
            'requestId' => $id,
            'form' => $form->createView()
        );
        return $this->render('UrbantCConvertBundle:ConvertRequest:edit.html.twig', $vars);
    }
    
    
    public function detailAction($id) {
        $repo = $this->getRepository('UrbantCConvertBundle:ConvertRequest');
        
        $request = $repo->find($id);
        if(!$request) {
            throw $this->createNotFoundException('ID:' . $id . ' was not found.');
        }
        
        $vars = array(
            'request' => $request
        );
        
        return $this->render('UrbantCConvertBundle:ConvertRequest:detail.html.twig', $vars);
    }
    
    
    public function apiPostAction() {
        $urls = $this->getRequest()->request->get('urls');
        
        $result = "OK";
        $errorMessage = "";
        
        $em = $this->getEntityManager();
        $convertRequestService = $this->get('urbant_cconvert.convert_request_service');
        
        $logger = $this->get('logger');
        
        try {
            if(is_array($urls)) {
                foreach($urls as $url) {
                    
                    $convertRequest = null;
                    $convertRequest = new ConvertRequest();
                    
                    //$convertRequest->setCreated(new DateTime());
                    $convertRequest->setUrl($url);
                    $convertRequest->setStatus(ConvertRequest::STATE_WAIT);
                    
                    //変換ルールの自動判定などを行ってから保存
                    $convertRequestService->saveRequest($convertRequest);
                    
                    $em->flush();
                    $logger->info($url);
                }
            }
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
            $result = "NG";
            $logger->info($e->getMessage());
        }
        
        return new Response($result . ":" . $errorMessage);
    }
}
