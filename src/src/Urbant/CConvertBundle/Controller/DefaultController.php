<?php

namespace Urbant\CConvertBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('UrbantCConvertBundle:Default:index.html.twig', array('name' => $name));
    }
}
