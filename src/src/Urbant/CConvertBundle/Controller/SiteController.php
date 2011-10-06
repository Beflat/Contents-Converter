<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class SiteController extends Controller
{
    
    public function indexAction()
    {
        return $this->render('UrbantCConvertBundle:Site:index.html.twig');
    }
}
