<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Urbant\CConvertBundle\Form\ContentSearchType;
use Urbant\CConvertBundle\Entity\Content;
use Urbant\CConvertBundle\Form\SiteType;

class ContentController extends BaseAdminController
{

    
    protected $pageCatId = 'content';
    
    /**
     * コンテンツの一覧表示
     * @param unknown_type $page
     */
    public function listAction()
    {
        $this->pageId = 'list';
        
        $em = $this->getDoctrine()->getEntityManager();
        $siteRepo = $em->getRepository('UrbantCConvertBundle:Content');

        //         $paginator = new \Zend_Paginator(new \Zend_Paginator_Adapter_Null(30));
        //         $paginator->setCurrentPageNumber($page);
        //         $paginator->setItemCountPerPage(10);


        $form = $this->createForm(new ContentSearchType());
        $request = $this->getRequest();
        $form->bindRequest($request);
        
        $searchConditions = $form->getData();
        
        $contents = $siteRepo->getContents($searchConditions);

        return $this->render('UrbantCConvertBundle:Content:list.html.twig',
            array('contents' => $contents, 'search_form' => $form->createView(),
        ));
    }


    /**
     * 削除等の処理の一括実行
     */
    public function batchAction($page) {

        //TODO: このままだとページング情報等を引き回せない。
        
        $this->pageId = 'list';
        
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('UrbantCConvertBundle:Site');
        $type = $request->get('type');

        switch($type) {
            case 'd':
                $repository->deleteSiteForIds($request->get('ids'));
                $this->get('session')->setFlash('message', '選択したデータを削除しました。');
                break;
            default:
                $this->get('session')->setFlash('message', '無効な区分です：' . $type);
        }

        return $this->indexAction($page);
    }

    /**
     * 新規登録画面の表示
     */
    public function addAction() {

        $this->pageId = 'add';
        
        $site = new Site();
        $form = $this->createForm(new SiteType(), $site);
        $vars = array(
            'form' => $form->createView(),
        );
        return $this->render('UrbantCConvertBundle:Site:add.html.twig', $vars);
    }


    /**
     * 新規登録書利の実行
     */
    public function createAction() {

        $this->pageId = 'add';
        
        $site = new Site();
        $form = $this->createForm(new SiteType(), $site);

        $request = $this->getRequest();
        $form->bindRequest($request);
        if($form->isValid()) {

            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($site);
            $em->flush();

            $this->get('session')->setFlash('message', '登録しました。');
            return $this->redirect($this->generateUrl('UrbantCConvertBundle_site_add'));
        }

        $vars = array(
            'form' => $form->createView(),
        );
        return $this->render('UrbantCConvertBundle:Site:add.html.twig', $vars);
    }


    /**
     * 編集画面の表示
     */
    public function editAction($id) {
        
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('UrbantCConvertBundle:Site');
        
        $site = $repository->find($id);
        if(!$site) {
            throw new $this->createNotFoundException('ID:' . $id . 'のサイトは存在しません。');
        }
        
        $form = $this->createForm(new SiteType(), $site);
        
        $vars = array(
            'siteId' => $id,
            'form' => $form->createView(),
        );
        return $this->render('UrbantCConvertBundle:Site:edit.html.twig', $vars);
    }
    
    
    public function updateAction($id) {
        
        $em = $this->getDoctrine()->getEntityManager();
        $site = $em->getRepository('UrbantCConvertBundle:Site')->find($id);
        if(!$site) {
            throw new $this->createNotFoundException('ID:' . $id . 'のサイトは存在しません。');
         }
        $form = $this->createForm(new SiteType(), $site);
        
        $request = $this->getRequest();
        $form->bindRequest($request);
        
        if($form->isValid()) {
            $em->flush();
            
            $this->get('session')->setFlash('message', 'サイト情報を更新しました。');
            $this->redirect($this->generateUrl('UrbantCConvertBundle_site_edit', array('id' => $id), true));
        }
        
        $vars = array(
            'siteId' => $id,
            'form' => $form->createView()
        );
        return $this->render('UrbantCConvertBundle:Site:edit.html.twig', $vars);
    }
    
    
    public function downloadAction($id) {
        
        $contentRepo = $this->getRepository('UrbantCConvertBundle:Content');
        
        $content = $contentRepo->find($id);
        if(!$content) {
            throw $this->createNotFoundException('ID:' . $id . ' was not found.');
        }
        
        //TODO: 基準ディレクトリをどうするか検討する
        $basePath = $this->container->getParameter('urbant_cconvert.content_dir_path');
        $filePath = $basePath . $content->getDataFilePath();
        
        if(!is_file($filePath)) {
            throw $this->createNotFoundException('Content file was not found.' . $filePath);
        }
        
        $contentData = @file_get_contents($filePath);
        
        $response = new Response($contentData);
        //$response->setContent('aaa');
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/epub+zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . basename($filePath) . '"');
//         prints the HTTP headers followed by the content
//         $response->send();
//         @readfile($filePath);
//         echo 'aaa';
        
        $content->setStatus($content::STATE_DOWNLOADED);
        $this->getEntityManager()->flush();
        
        return $response;        
        }
}
