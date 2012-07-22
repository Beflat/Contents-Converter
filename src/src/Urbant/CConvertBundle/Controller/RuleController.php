<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Urbant\CConvertBundle\Form\RuleSearchType;
use Urbant\CConvertBundle\Form\RuleType;
use Urbant\CConvertBundle\Entity\Rule;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;

class RuleController extends BaseAdminController
{

    
    protected $pageCatId = 'rule';
    
    /**
     * 変換ルールの一覧表示
     */
    public function listAction()
    {
        $this->pageId = 'list';
        
        $em = $this->getDoctrine()->getEntityManager();
        $ruleRepo = $em->getRepository('UrbantCConvertBundle:Rule');

        $form = $this->createForm(new RuleSearchType());
        $request = $this->getRequest();
        $form->bindRequest($request);
        
        $searchConditions = $form->getData();
        
        //TODO: 全体的に使用するのでどこか共通の場所で取得できるようにする。
        $user = $this->get('security.context')->getToken()->getUser();
        
        $qb = $ruleRepo->getQueryBuilderForSearch($user, $searchConditions);

        
        $adapter = new DoctrineORMAdapter($qb);
        $pagerfanta = new Pagerfanta($adapter);
        $pagerfanta->setMaxPerPage(20);
        $pagerfanta->setCurrentPage($request->attributes->get('page', 1));
        
        $nbResults = $pagerfanta->getNbResults();
        $rules = $pagerfanta->getCurrentPageResults();
        
        $vars = array(
        	'rules' => $rules, 
        	'search_form' => $form->createView(),
        	'pager' => $pagerfanta
        );
        
        return $this->render('UrbantCConvertBundle:Rule:index.html.twig', $vars);
    }


    /**
    * 削除等の処理の一括実行
    */
    public function batchAction($page) {
    
        //TODO: このままだとページング情報等を引き回せない。
    
        $this->pageId = 'list';
    
        $request = $this->getRequest();
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('UrbantCConvertBundle:Rule');
        $user = $this->get('security.context')->getToken()->getUser();
        $type = $request->get('type');
    
        switch($type) {
            case 'd':
                $repository->deleteRuleForIds($user, $request->get('ids'));
                $this->get('session')->setFlash('message', '選択したデータを削除しました。');
                break;
            default:
                $this->get('session')->setFlash('message', '無効な区分です：' . $type);
        }
    
        return $this->listAction($page);
    }
    
    public function addAction()
    {
        $this->pageId = 'add';
        
        $rule = new Rule();
        $form = $this->createForm(new RuleType(), $rule);
        
        return $this->render('UrbantCConvertBundle:Rule:add.html.twig',
            array('form' => $form->createView(),
        ));
    }

    public function createAction()
    {
        $this->pageId = 'add';
        
        $rule = new Rule();
        
        $form = $this->createForm(new RuleType(), $rule);
        $request = $this->getRequest();
        $form->bindRequest($request);

        if($form->isValid()) {
            $user = $this->get('security.context')->getToken()->getUser();
            $rule->setUserId($user);
            
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($rule);
            
            $em->flush();
            
            $this->get('session')->setFlash('rule.add.message', '変換ルールを保存しました。');
            $this->redirect('UrbantCConvertBundle_add');
        }
        
        return $this->render('UrbantCConvertBundle:Rule:add.html.twig',
            array('form' => $form->createView(),
        ));
    }
    
    public function editAction($id)
    {
        $this->pageId = '';
        
        $em = $this->getDoctrine()->getEntityManager();
        $repository = $em->getRepository('UrbantCConvertBundle:Rule');
    
        $user = $this->get('security.context')->getToken()->getUser();
        
        $rule = $repository->find($id);
        if(!$rule || $rule->getUserId() != $user->getId()) {
            throw new $this->createNotFoundException('ID:' . $id . 'の変換ルールは存在しません。');
        }
        
        $form = $this->createForm(new RuleType(), $rule);
    
        return $this->render('UrbantCConvertBundle:Rule:edit.html.twig',
        array('form' => $form->createView(),
            'ruleId' => $id
        ));
    }
    
    
    public function updateAction($id) {
    
        $em = $this->getDoctrine()->getEntityManager();
        $user = $this->get('security.context')->getToken()->getUser();        
        
        $rule = $em->getRepository('UrbantCConvertBundle:Rule')->find($id);
        if(!$rule || $user->getUserId() != $user->getId()) {
            throw new $this->createNotFoundException('ID:' . $id . 'の変換ルールは存在しません。');
        }
        $form = $this->createForm(new RuleType(), $rule);
    
        $request = $this->getRequest();
        $form->bindRequest($request);
    
        if($form->isValid()) {
            $em->flush();
    
            $this->get('session')->setFlash('rule.edit.message', '変換ルールを更新しました。');
            $this->redirect($this->generateUrl('UrbantCConvertBundle_rule_edit', array('id' => $id), true));
        }
    
        $vars = array(
                'ruleId' => $id,
                'form' => $form->createView()
        );
        return $this->render('UrbantCConvertBundle:Rule:edit.html.twig', $vars);
    }
    
}
