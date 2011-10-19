<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Urbant\CConvertBundle\Form\SiteSearchType;
use Urbant\CConvertBundle\Entity\Site;
use Urbant\CConvertBundle\Form\SiteType;

class SiteController extends BaseAdminController
{

    
    protected $pageCatId = 'site';
    
    /**
     * サイトの一覧表示
     * @param unknown_type $page
     */
    public function indexAction($page)
    {
        $this->pageId = 'list';
        
        $em = $this->getDoctrine()->getEntityManager();
        $siteRepo = $em->getRepository('UrbantCConvertBundle:Site');

        //         $paginator = new \Zend_Paginator(new \Zend_Paginator_Adapter_Null(30));
        //         $paginator->setCurrentPageNumber($page);
        //         $paginator->setItemCountPerPage(10);


        $form = $this->createForm(new SiteSearchType());
        $request = $this->getRequest();
        $form->bindRequest($request);
        
        $searchConditions = $form->getData();
        
        $sites = $siteRepo->getSites($searchConditions);

        return $this->render('UrbantCConvertBundle:Site:index.html.twig',
            array('sites' => $sites, 'search_form' => $form->createView(),
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
}
