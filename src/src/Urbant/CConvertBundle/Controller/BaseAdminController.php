<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

use Urbant\CConvertBundle\Form\SiteSearchType;
use Urbant\CConvertBundle\Entity\Site;
use Urbant\CConvertBundle\Form\SiteType;


/**
 * 管理画面用共通コントローラ
 */
abstract class BaseAdminController extends Controller
{

    protected $pageId;

    protected $pageCatId;


    /**
     * @var array
     */
    protected $menues;

    /**
     * テンプレートに渡すパラメータ。現時点で以下の名前が予約されている。
     *
    * _pageId: ページID
    * _catId: カテゴリID
     *
    * @var unknown_type
     */
    protected $templateParams;

    public function __construct() {

    }

    protected function initMenues() {
        //メニュー定義。generateUrlを使用するとコンストラクタでは定義できない。
        //TODO:ここではなく定義ファイル等に定義する
        $this->menues = array(
//             'dashboard' => array(
//                 'name' => 'DASH BOARD',
//                     'url' => '#',
//                 'sub' => array(
//                     'list' => array('name' => 'LIST', 'url' => ''),
//                    ),
//              ),
//             'site' => array(
//                 'name' => 'SITE',
//                 'url' => $this->generateUrl('UrbantCConvertBundle_site_list'),
//                 'sub' => array(
//                     'list' => array('name' => 'LIST', 'url' => $this->generateUrl('UrbantCConvertBundle_site_list')),
//                     'add' => array('name' => 'ADD', 'url' => $this->generateUrl('UrbantCConvertBundle_site_add')),
//                     ),
//               ),
            'rule' => array(
                'name' => 'RULE',
                'url' => $this->generateUrl('UrbantCConvertBundle_rule_list'),
                'sub' => array(
                    'list' => array('name' => 'LIST', 'url' => $this->generateUrl('UrbantCConvertBundle_rule_list')),
                    'add' => array('name' => 'ADD', 'url' => $this->generateUrl('UrbantCConvertBundle_rule_add')),
                    ),
              ),
            'request' => array(
                'name' => 'REQUEST',
                'url' => $this->generateUrl('UrbantCConvertBundle_request_list'),
                'sub' => array(
                    'list' => array('name' => 'LIST', 'url' => $this->generateUrl('UrbantCConvertBundle_request_list')),
                    'add' => array('name' => 'ADD', 'url' => $this->generateUrl('UrbantCConvertBundle_request_add')),
                    ),
              ),
            'content' => array(
                'name' => 'CONTENT',
                'url' => $this->generateUrl('UrbantCConvertBundle_content_list'),
                'sub' => array(
                    'list' => array('name' => 'LIST', 'url' => $this->generateUrl('UrbantCConvertBundle_content_list')),
                    ),
              ),
        );
        
        //管理者の場合のみ、ユーザー管理メニューを表示する
        $securityContext = $this->get('security.context');
        if($securityContext->isGranted('ROLE_ADMIN')) {
            $this->menues['user'] = array(
                'name' => 'USER',
                'url' => $this->generateUrl('UrbantCConvertBundle_user_list'),
                'sub' => array(
                    'list' => array('name' => 'LIST', 'url' => $this->generateUrl('UrbantCConvertBundle_user_list')),
                    'add' => array('name' => 'ADD', 'url' => $this->generateUrl('UrbantCConvertBundle_user_add')),
                ),
            );
        }
    }
    
    
    /**
     * 共通の設定パラメータを含めた状態でレンダリングを行う。
     */
    public function render($view, array $parameters = array(), Response $response = null) {
        
        $this->initMenues();
        
        $currentSubMenues = array();
        foreach ($this->menues as $menuId => $menu) {
            if($this->pageCatId == $menuId) {
            	$currentSubMenues = $menu['sub'];
            }
            
        }
        
        //共通のパラメータ
        $this->templateParams = array(
           '_pageId' => $this->pageId,
           '_catId' => $this->pageCatId,
           '_menues' => $this->menues,
           '_subMenues' => $currentSubMenues
        );

        //呼び出し元のパラメータで上書きする
        foreach((array)$parameters as $key=> $value) {
            $this->templateParams[$key] = $value;
        }
        return parent::render($view, $this->templateParams, $response);
    }
    
    
    //---------------------------------------------------------------------
    //各コントローラで共通して呼び出す処理のショートカット
    
    protected function getEntityManager() {
        return $this->getDoctrine()->getEntityManager();
    }
    
    
    protected function getRepository($entityName) {
        return $this->getEntityManager()->getRepository($entityName);
    }
}
