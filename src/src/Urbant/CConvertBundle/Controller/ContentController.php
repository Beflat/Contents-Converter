<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Component\HttpFoundation\Response;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Urbant\CConvertBundle\Form\ContentSearchType;
use Urbant\CConvertBundle\Entity\Content;
use Urbant\CConvertBundle\Form\SiteType;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

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
        $contentRepo = $em->getRepository('UrbantCConvertBundle:Content');

        $form = $this->createForm(new ContentSearchType());
        $request = $this->getRequest();
        $form->bindRequest($request);

        $searchConditions = $form->getData();
        $qb = $contentRepo->getQueryBuilderForSearch($searchConditions);

        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(20);
        $pagerfanta->setCurrentPage($request->attributes->get('page', 1));

        $contents = $pagerfanta->getCurrentPageResults();
        $searchResultsCount = $pagerfanta->getNbResults();
        $currentPage = $pagerfanta->getCurrentPage();

        $vars = array(
            'contents' => $contents,
            'search_form' => $form->createView(),
            'pager' => $pagerfanta,
            'search_results_count' => $searchResultsCount
        );
        return $this->render('UrbantCConvertBundle:Content:list.html.twig', $vars);
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
            throw new $this->createNotFoundException('ID:' . $id . 'のコンテンツは存在しません。');
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
            throw new $this->createNotFoundException('ID:' . $id . 'のコンテンツは存在しません。');
        }
        $form = $this->createForm(new SiteType(), $site);

        $request = $this->getRequest();
        $form->bindRequest($request);

        if($form->isValid()) {
            $em->flush();

            $this->get('session')->setFlash('message', 'コンテンツ情報を更新しました。');
            $this->redirect($this->generateUrl('UrbantCConvertBundle_site_edit', array('id' => $id), true));
        }

        $vars = array(
            'siteId' => $id,
            'form' => $form->createView()
        );
        return $this->render('UrbantCConvertBundle:Site:edit.html.twig', $vars);
    }


    public function downloadAction($id) {

        $contentService = $this->get('urbant_cconvert.content_service');

        $contentRepo = $this->getRepository('UrbantCConvertBundle:Content');

        $content = $contentRepo->find($id);
        if(!$content) {
            throw $this->createNotFoundException('ID:' . $id . ' was not found.');
        }

        $filePath = $contentService->getContentFilePath($content);

        if(!is_file($filePath)) {
            throw $this->createNotFoundException('Content file was not found.' . $filePath);
        }

        $contentData = @file_get_contents($filePath);

        $response = new Response($contentData);
        //$response->setContent('aaa');
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/epub+zip');
        $response->headers->set('Content-Disposition', 'attachment;filename="' . basename($filePath) . '"');
        $response->headers->set('Content-Length', strlen($contentData));
        //         $response->send();
        //         @readfile($filePath);
        //         echo 'aaa';

        $content->setStatus($content::STATE_DOWNLOADED);
        $this->getEntityManager()->flush();

        return $response;
    }


    /**
     * WebAPIによるコンテンツ一覧取得
     */
    public function apiGetAction()
    {
        $this->pageId = 'list';

        $em = $this->getDoctrine()->getEntityManager();
        $contentRepo = $em->getRepository('UrbantCConvertBundle:Content');
        
        $qb = $contentRepo->getQueryBuilderForSearch(array());
        $contents = $qb->getQuery()->getResult();
        
        $contentApiService = $this->get('urbant_cconvert.content_api_service');
        $xmlContent = $contentApiService->getContentXml($contents);
        
        $response = new Response($xmlContent);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'text/xml');
        
        return $response;
    }

}
