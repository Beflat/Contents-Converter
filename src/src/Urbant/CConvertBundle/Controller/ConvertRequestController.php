<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Urbant\CConvertBundle\Form\ConvertRequestSearchType;
use Urbant\CConvertBundle\Entity\ConvertRequest;
use Urbant\CConvertBundle\Form\ConvertRequestType;

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

        //         $paginator = new \Zend_Paginator(new \Zend_Paginator_Adapter_Null(30));
        //         $paginator->setCurrentPageNumber($page);
        //         $paginator->setItemCountPerPage(10);


        $form = $this->createForm(new ConvertRequestSearchType());
        $request = $this->getRequest();
        $form->bindRequest($request);
        
        $searchConditions = $form->getData();
        
        $requests = $reqRepo->getRequests($searchConditions);

        return $this->render('UrbantCConvertBundle:ConvertRequest:list.html.twig',
            array('requests' => $requests, 'search_form' => $form->createView(),
        ));
    }


    /**
     * 削除等の処理の一括実行
     */
    public function batchAction($page) {

        //TODO: このままだとページング情報等を引き回せない。
        
        $this->pageId = 'list';
        
        $request = $this->getRequest();
        $repository = $this->getRepository('UrbantCConvertBundle:ConvertRequest');
        $type = $request->get('type');

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
            $em->persist($convertRequest);
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
}
