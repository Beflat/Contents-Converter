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
            'dashboard' => array(
                'name' => 'Dash Board',
                    'url' => '#',
                'sub' => array(
                    'list' => array('name' => 'LIST', 'url' => ''),
                   ),
             ),
            'site' => array(
                'name' => 'Site',
                'url' => $this->generateUrl('UrbantCConvertBundle_site_list'),
                'sub' => array(
                    'list' => array('name' => 'LIST', 'url' => $this->generateUrl('UrbantCConvertBundle_site_list')),
                    'add' => array('name' => 'ADD', 'url' => $this->generateUrl('UrbantCConvertBundle_site_add')),
                    ),
              ),
            'rule' => array(
                'name' => 'Rule',
                'url' => $this->generateUrl('UrbantCConvertBundle_rule_list'),
                'sub' => array(
                    'list' => array('name' => 'LIST', 'url' => $this->generateUrl('UrbantCConvertBundle_rule_list')),
                    'add' => array('name' => 'ADD', 'url' => $this->generateUrl('UrbantCConvertBundle_rule_add')),
                    ),
              ),
        );
    }
    
    
    /**
     * 共通の設定パラメータを含めた状態でレンダリングを行う。
     */
    public function render($view, array $parameters = array(), Response $response = null) {
        
        $this->initMenues();
        
        //共通のパラメータ
        $this->templateParams = array(
           '_pageId' => $this->pageId,
           '_catId' => $this->pageCatId,
           '_menues' => $this->menues,
        );

        //呼び出し元のパラメータで上書きする
        foreach((array)$parameters as $key=> $value) {
            $this->templateParams[$key] = $value;
        }
        return parent::render($view, $this->templateParams, $response);
    }
}
