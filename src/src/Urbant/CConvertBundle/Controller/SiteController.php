<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SiteController extends Controller
{
    
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $siteRepo = $em->getRepository('UrbantCConvertBundle:Site');
        
        $sites = $siteRepo->getSites();
        
        return $this->render('UrbantCConvertBundle:Site:index.html.twig',
            array('sites' => $sites)
        );
    }
    
    
    public function batchAction() {
    }
}
